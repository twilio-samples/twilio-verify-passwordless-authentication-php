<?php

declare(strict_types=1);

namespace App;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App as SlimApp;
use SlimSession\Helper as SlimSessionHelper;
use Slim\Interfaces\RouteInterface;
use Slim\Middleware\ContentLengthMiddleware;
use Slim\Views\Twig;
use Twilio\Rest\Client;

use function assert;

/**
 * This class encapsulates the central Slim application,
 * making it easier to create and test.
 */
final class Application
{
    private SlimSessionHelper $session;
    private string $verifyServiceSid;

    public function __construct(private readonly SlimApp $app)
    {
        $app->add(new ContentLengthMiddleware());
        $app->addBodyParsingMiddleware();
        $app->addRoutingMiddleware();
        $app->addErrorMiddleware(true, true, true);

        $this->session          = new SlimSessionHelper();
        $this->verifyServiceSid = $_ENV['TWILIO_VERIFY_SERVICE_SID'];
    }

    /**
     * setupRoutes defines the application's routing table
     */
    public function setupRoutes(): void
    {
        // Render the "Sign In" form
        $this->app->get('/', [$this, 'viewSignInForm']);
        // Process the "Sign In" form
        $this->app->post('/', [$this, 'handleSignIn']);

        // Render the "Verify OTP code" form
        $this->app->get('/verify', [$this, 'viewVerifyOtpForm']);
        // Process the "Verify OTP code" form
        $this->app->post('/verify', [$this, 'handleVerifyOtp']);
    }

    /**
     * getRoutes returns the application's current routes
     *
     * @return RouteInterface[]
     */
    private function getRoutes(): array
    {
        return $this->app->getRouteCollector()->getRoutes();
    }

    /**
     * run launches the application
     */
    public function run(): void
    {
        $this->app->run();
    }

    /**
     * This renders the sign-in form where users can enter and submit their
     * phone numbers to request an OTP code
     */
    public function viewSignInForm(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'signin.html.twig', []);
    }

    /**
     * This sends an OTP code to the user's phone number
     */
    public function handleSignIn(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $postData = $request->getParsedBody();
        $phone    = $postData['phone'] ?? '';
        if ($phone === '') {
            $response
                ->withHeader('Location', '/')
                ->withStatus(StatusCodeInterface::STATUS_FOUND);
        }

        $this->session->phone = $postData['phone'];

        $twilio = $this->app->getContainer()->get(Client::class);
        assert($twilio instanceof Client);

        $verification = $twilio->verify->v2
            ->services($this->verifyServiceSid)
            ->verifications
            ->create($postData['phone'], "sms");

        $response = $response
            ->withHeader('Location', '/verify')
            ->withStatus(StatusCodeInterface::STATUS_FOUND);

        return $response;
    }

    /**
     * This renders the form where users can verify the OTP code that they have
     * received via SMS
     */
    public function viewVerifyOtpForm(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'verifyotp.html.twig', []);
    }

    /**
     * This verifies the OTP code that the user submitted in the verify OTP
     * code form
     */
    public function handleVerifyOtp(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        if (! $this->session->exists('phone') || $this->session->get('phone', '') === '') {
            $response = $response
                ->withHeader('Location', '/')
                ->withStatus(StatusCodeInterface::STATUS_FOUND);

            return $response;
        }

        $postData = $request->getParsedBody();
        $code     = $postData['code'] ?? '';
        if ($code === '') {
            $response = $response
                ->withHeader('Location', '/verify')
                ->withStatus(StatusCodeInterface::STATUS_FOUND);

            return $response;
        }

        $twilio = $this->app->getContainer()->get(Client::class);
        assert($twilio instanceof Client);

        $verificationCheck = $twilio->verify->v2
            ->services($this->verifyServiceSid)
            ->verificationChecks
            ->create([
                "code" => $postData['code'],
                "to"   => $this->session->phone,
            ]);

        $this->session->delete('phone');

        return Twig::fromRequest($request)
            ->render(
                $response,
                'verification-status.html.twig',
                [
                    'status'  => $verificationCheck->status === 'approved',
                    'message' => $verificationCheck->status === 'approved'
                        ? "Verification was successful"
                        : "Verification failed",
                ],
            );
    }
}

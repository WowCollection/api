<?php

namespace App\Utils;

use Twig\Environment;

/**
 * Class Mailer
 * @package App\Utils
 */
class Mailer
{
    /** @var \Swift_Mailer $mailer */
    protected $mailer;

    /** @var Environment $twig */
    private $twig;

    /** @var Locale $locale */
    private $locale;

    /** @var string $mailSender */
    private $mailSender;

    /**
     * Mailer constructor.
     * @param Environment $twig
     * @param \Swift_Mailer $mailer
     * @param Locale $locale
     * @param string $mailSender
     */
    public function __construct(Environment $twig, \Swift_Mailer $mailer, Locale $locale, string $mailSender)
    {
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->locale = $locale;
        $this->mailSender = $mailSender;
    }

    /**
     * @param $view
     * @param array $parameters
     * @return string
     */
    public function renderView($view, array $parameters = [])
    {
        return $this->twig->render($view, $parameters);
    }

    public function sendMail(string $subject, string $to, string $viewPath, array $viewParameters = null)
    {
        $viewParameters['locale'] = $this->locale;

        $mail =  new \Swift_Message();
        $mail = $mail
            ->setSubject($subject)
            ->setFrom($this->mailSender)
            ->setTo($to)
            ->setBody($this->renderView($viewPath, $viewParameters), "text/html");

        $this->mailer->send($mail);
    }
}

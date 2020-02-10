<?php
class Mail
{
	// отправка сообщения
	public static function send($subject, $body, $to, $fromEmail = null, $fromName = null)
	{
		require_once APPLICATION_DIR . '/Lib/Swift/swift_required.php';
		// кол-во отправленных сообщений
		try
		{
			// Choose transport
			// TODO: поменять на параметры из настроек
			$transport = Swift_SmtpTransport::newInstance(Configurator::get('smtp:server'), Configurator::get('smtp:port'), 'ssl');
			$transport->setUsername(Configurator::get('smtp:login'));
			$transport->setPassword(Configurator::get('smtp:password'));

			// Create the Mailer using your created Transport
			$mailer = Swift_Mailer::newInstance($transport);

			// Create the message
			$message = Swift_Message::newInstance();
			$message->setSubject($subject);
			$message->setBody($body, 'text/html', 'utf-8');
			// Addressed to
			if (!is_array($to))
				$to = explode(',', $to);

			$message->setTo($to);
			// From
			$fromEmail = $fromEmail ? $fromEmail : Configurator::get('mail:from');
			$fromName = $fromName ? $fromName : Configurator::get('mail:fromName');
			$message->setFrom(array($fromEmail => $fromName));
			$numSent = $mailer->send($message);

		}
		catch (Exception $e)
		{
			$numSent = false;
		}

		spl_autoload_register(array("Configurator", "autoload"));

		return $numSent;

	}

}

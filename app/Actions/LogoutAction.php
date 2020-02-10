<?php
/**
* Выход с сайта
*
*/
class LogoutAction extends AuthorizedUserAction implements IPublicAction
{
	public function execute()
	{
		Context::logOff();
		sleep(1);
		Enviropment::setCookie('forum_cookie', '');
		Enviropment::redirect("userlogin", "Всего доброго, заходите ещё!");

	}
}

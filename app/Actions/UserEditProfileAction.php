<?php
/**
* Действие для редактирования пользователя
*
*/
class UserEditProfileAction extends AuthorizedUserAction implements IPublicAction
{
	public function execute()
	{
		$lastName = FilterInput::add(new StringFilter("lastName", false, "Фамилия"));
		$firstName = FilterInput::add(new StringFilter("firstName", false, "Имя"));
		$secondName = FilterInput::add(new StringFilter("secondName", false, "Отчество"));
		$phone1 = FilterInput::add(new StringFilter("phone1", false, "Телефон"));
		$phone2 = FilterInput::add(new StringFilter("phone2", false, "Ещё один телефон"));
		$name = FilterInput::add(new StringFilter("name", false, "Организация"));
		$reqorg = FilterInput::add(new StringFilter("reqorg", false, "Организатор"));
		$reqopt = FilterInput::add(new StringFilter("reqopt", false, "Поставщик"));

		// проверить телефон
		// допускаются только цифры
		if (!preg_match('/^\d{10}$/', $phone1))
			FilterInput::addMessage("Неверный формат телефона");

		if ($phone2 && !preg_match('/^\d{10}$/', $phone2))
			FilterInput::addMessage("Неверный формат второго телефона");

		if (!FilterInput::isValid())
		{
			FormRestore::add("user-register");
			Enviropment::redirectBack(FilterInput::getMessages());
		}

		// формируем массив данных
		$regData = compact("lastName", "firstName", "secondName", "phone1", "phone2", "name", "reqorg", "reqopt", "city");

		$um = new UserManager();

		// начало транзакции
		$um->startTransaction();
		try
		{
			$getUser = $um->updateUser($this->ownerSiteId, $this->ownerOrgId, $regData);
		}
		catch (Exception $e)
		{
			$um->rollbackTransaction();
			Logger::error($e->getMessage());
			Enviropment::redirectBack("Не удалось сохранить данные, попробуйте позднее или сообщите администратору об ошибке");
		}

		$um->commitTransaction();

		// проверим добавлен ли новый орг
		if (!$getUser)
			Enviropment::redirectBack("Не удалось сохранить данные, попробуйте позднее или сообщите администратору об ошибке");
		else
			Enviropment::redirect("userarea", "Данные сохранены");

	}

}

<?php

require_once '../Config/framework.php';
require_once '../BaseApplication.php';

Logger::init(Configurator::getSection("logger"));

// погружаем все переменные с потока
$vars = $_POST;

// сохраним в логер
Logger::pay('W1 vars:');
Logger::pay($vars);

// ключ определяем по номеру кошелька ключик
$skey = '';
if (strtoupper($vars['WMI_MERCHANT_ID']) == '193537155072')
	$skey = 'elIxWXRTc15pcllgbXg2YUx1a2I1MW9RTWBf';

$pm = new PurseManager();
$purseObj = $pm->getByMerchantId(strtoupper($vars['WMI_MERCHANT_ID']));
if ($purseObj)
	$skey = base64_decode($purseObj->skey);

// Проверка наличия необходимых параметров в POST-запросе
if (!isset($vars['WMI_SIGNATURE']))
	print_answer('RETRY', 'Отсутствует параметр WMI_SIGNATURE');

if (!isset($vars['WMI_PAYMENT_NO']))
	print_answer('CANCEL', 'Отсутствует параметр WMI_PAYMENT_NO');

if (!isset($vars['WMI_ORDER_STATE']))
	print_answer('RETRY', 'Отсутствует параметр WMI_ORDER_STATE');

if (intval($vars['WMI_PAYMENT_NO']) == 0)
	print_answer('CANCEL', 'Параметр WMI_PAYMENT_NO равен нулю');

// Извлечение всех параметров POST-запроса, кроме WMI_SIGNATURE
foreach ($vars as $name => $value)
{
	if ($name !== 'WMI_SIGNATURE')
		$params[$name] = $value;
}

// Сортировка массива по именам ключей в порядке возрастания
// и формирование сообщения, путем объединения значений формы
uksort($params, 'strcasecmp');
$values = '';

foreach ($params as $name => $value)
{
	$values .= $params[$name];
}

// Формирование подписи для сравнения ее с параметром WMI_SIGNATURE
$signature = base64_encode(pack('H*', md5($values . $skey)));

// Сравнение полученной подписи с подписью W1
if ($signature == $vars['WMI_SIGNATURE'])
{
	if (strtoupper($vars['WMI_ORDER_STATE']) == 'ACCEPTED')
	{
		// TODO: Пометить заказ, как «Оплаченный» в системе учета магазина
		makesale($vars);
		print_answer('OK', 'Заказ N'.$vars['WMI_PAYMENT_NO'].' оплачен!');
	}
	else if (strtoupper($vars['WMI_ORDER_STATE']) == 'PROCESSING')
	{
		// TODO: Пометить заказ, как «Оплаченный» в системе учета магазина
		makesale($vars);
		print_answer('OK', 'Заказ N'.$vars['WMI_PAYMENT_NO'].' оплачен!');
		// Данная ситуация возникает, если в платежной форме WMI_AUTO_ACCEPT=0.
		// В этом случае интернет-магазин может принять оплату или отменить ее.
	}
	else if (strtoupper($vars["WMI_ORDER_STATE"]) == "REJECTED")
	{
		// TODO: Пометить заказ, как «Неоплаченный» в системе учета магазина
		// НАДО СДЕЛАТЬ ОТКАТ !!!, т.к. платежи могуть быть откачены !!!
		print_answer('OK', 'Заказ N'.$vars['WMI_PAYMENT_NO'].' отменен!');
        Logger::pay('Заказ N'.$vars['WMI_PAYMENT_NO'].' отменен!');
	}
	else
	{
		// Случилось что-то странное, пришло неизвестное состояние заказа
		print_answer('RETRY', 'Неверное состояние '.$vars['WMI_ORDER_STATE']);
	}
}
else
{
	// Подпись не совпадает, возможно вы поменяли настройки интернет-магазина
	print_answer('RETRY', 'Неверная подпись '.$vars['WMI_SIGNATURE']);
}


// продавец будет принимать решение о продаже
function makesale($vars)
{
	// при оплате закупок finanses
	if (strpos($vars['WMI_SUCCESS_URL'], 'finanses') !== false)
	{
		Logger::pay("ok, do pay zakupka");

		$pm = new PayManager();

		$w1ans = "";
		$pm->startTransaction();
		try
		{
			Logger::pay("userpay startTransaction");

			$orderAmount = floatval(str_replace(',', '.', $vars['WMI_PAYMENT_AMOUNT']));
			$orderId = intval($vars['WMI_PAYMENT_NO']) - 1000000;

			if ($orderId <= 0)
			{
				$w1ans = 'Не верный N заказа '.intval($vars['WMI_PAYMENT_NO']);
				throw new Exception($w1ans);
			}

			$payObj = $pm->getById($orderId);
			if (!$payObj)
			{
				$w1ans = 'Не найден счет N'.intval($vars['WMI_PAYMENT_NO']);
				throw new Exception($w1ans);
			}

			$userId = $payObj->userId;
			$headId = $payObj->headId;
			$amount = $orderAmount;

			$om = new OrderHeadManager();
			$orderHeadObj = $om->getByHeadAndUserId($headId, $userId);
			if (!$orderHeadObj)
			{
				$w1ans = 'Не найден заказ по счету N'.intval($vars['WMI_PAYMENT_NO']);
				throw new Exception($w1ans);
			}

			// надо поменять статус платежа в pay
			$payObj->dateConfirm = time();
			$payObj->status = Pay::STATUS_SUCCED;
			$pm->save($payObj);

            // получим доставку и сумму по ней
            $oom = new OfficeOrderManager();
            $officeOrderObj = $oom->getByOrderIdUserId($orderHeadObj->id, $payObj->userId);

            $otherPayment = $amount - $orderHeadObj->payHold;

            // убрать из заказа pendingAmount в сумме платежа
            if ($otherPayment > 0)
            {
                $orderHeadObj->payAmount = $orderHeadObj->payHold;
                $orderHeadObj->payHold = 0;
				$orderHeadObj->datePaymentConfirm = time();

                // $otherPayment можно использовать в счёт погашения доставки
                if (!$officeOrderObj)
                {
                    Logger::info("There are {$otherPayment} RUR left after orgpayconfirm of pay object id {$payObj->id}");
                }
                else
                {
                    $needPayOffice = $officeOrderObj->price - $officeOrderObj->payHold - $officeOrderObj->payAmount + $officeOrderObj->payBackAmount;
                    if ($needPayOffice == 0)
                    {
                        Logger::info("There are {$otherPayment} RUR left after orgpayconfirm of pay object id {$payObj->id}");
                    }
                    else
                    {
                        $otherPaymentAfterPayOffice = $otherPayment - $officeOrderObj->payHold;

                        $officeOrderObj->payHold = $officeOrderObj->payHold - $needPayOffice;
                        $officeOrderObj->payAmount = $officeOrderObj->payAmount + $needPayOffice;
                        $oom->save($officeOrderObj);

                        if ($otherPaymentAfterPayOffice > 0)
                            Logger::info("There are {$otherPaymentAfterPayOffice} RUR left after orgpayconfirm of pay office object id {$payObj->id}");

                    }
                }
            }
            else
            {
                $orderHeadObj->payHold = $orderHeadObj->payHold - $amount;
                $orderHeadObj->payAmount = $orderHeadObj->payAmount + $amount;
				$orderHeadObj->datePaymentConfirm = time();
            }

            $om->save($orderHeadObj);

		}
		catch (Exception $e)
		{
			$pm->rollbackTransaction();
			Logger::error($e);
			print_answer('CANCEL', $w1ans);
		}

		$pm->commitTransaction();

	}

	// по $backUrl определим что будем делать
	if (strpos($vars['WMI_SUCCESS_URL'], 'orgcommision') !== false)
	{
		Logger::pay("ok, do orgcommision");

		$scm = new SiteCommisionManager();

		$w1ans = "";
		$scm->startTransaction();
		try
		{
			Logger::pay("orgcommision startTransaction");

			$orderAmount = floatval(str_replace(',', '.', $vars['WMI_PAYMENT_AMOUNT']));
			$orderId = intval($vars['WMI_PAYMENT_NO']) - 10000;
			if ($orderId <= 0)
			{
				$w1ans = 'Не верный N заказа '.intval($vars['WMI_PAYMENT_NO']);
				throw new Exception($w1ans);
			}

			$scObj = $scm->getById($orderId);
			if (!$scObj)
			{
				$w1ans = 'Не найден заказ N'.intval($vars['WMI_PAYMENT_NO']);
				throw new Exception($w1ans);
			}

			// если нашли, поменяем статус заказа на "оплачено"
			// запишем сумму сколько было оплачено

			$scObj->payAmount = $orderAmount;
			$scObj->dateConfirm = time();
			$scObj->way = SiteCommision::WAY_EK;
			$scObj->status = SiteCommision::STATUS_SUCCED;

			$scm->save($scObj);

			Logger::pay("orgcommision scObj saved");

		}
		catch (Exception $e)
		{
			$scm->rollbackTransaction();
			Logger::error($e);
			print_answer('CANCEL', $w1ans);
		}

		$scm->commitTransaction();


	}
	else
	{
		Logger::pay("no substring");
	}


	return true;
}


// преобразует переменную из post
function clean($arg)
{
    return trim(stripslashes(htmlentities($arg, ENT_QUOTES)));
}


// функция - посредник - выход в случае ошибки и т.д. и т.п.
function doExit()
{
	exit();
}


// выход с ответом системе W1
function print_answer($result, $description)
{
	// echo "WMI_RESULT=".strtoupper($result)."&"."WMI_DESCRIPTION=".$description.'<br>';
	Logger::pay("W1 answer:");
	Logger::pay("WMI_RESULT=".strtoupper($result)."&"."WMI_DESCRIPTION=".$description);
    print "WMI_RESULT=" . strtoupper($result) . "&";
    print "WMI_DESCRIPTION=" .urlencode($description);
    doExit();
}

?>
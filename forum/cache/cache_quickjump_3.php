<?php

if (!defined('FORUM')) exit;
define('FORUM_QJ_LOADED', 1);
$forum_id = isset($forum_id) ? $forum_id : 0;

?><form id="qjump" method="get" accept-charset="utf-8" action="http://massapokupok.ru/forum/viewforum.php">
	<div class="frm-fld frm-select">
		<label for="qjump-select"><span><?php echo $lang_common['Jump to'] ?></span></label><br />
		<span class="frm-input"><select id="qjump-select" name="id">
			<optgroup label="Обсуждение сайта MassaPokupok.ru">
				<option value="1"<?php echo ($forum_id == 1) ? ' selected="selected"' : '' ?>>Работа сайта</option>
				<option value="8"<?php echo ($forum_id == 8) ? ' selected="selected"' : '' ?>>Откройте закупку</option>
			</optgroup>
			<optgroup label="г.Йошкар-Ола">
				<option value="2"<?php echo ($forum_id == 2) ? ' selected="selected"' : '' ?>>Закупки</option>
				<option value="4"<?php echo ($forum_id == 4) ? ' selected="selected"' : '' ?>>Болталка участников</option>
				<option value="5"<?php echo ($forum_id == 5) ? ' selected="selected"' : '' ?>>Хвастики</option>
			</optgroup>
			<optgroup label="Предложения оптовиков организаторам">
				<option value="7"<?php echo ($forum_id == 7) ? ' selected="selected"' : '' ?>>Предложения оптовиков организаторам</option>
			</optgroup>
		</select>
		<input type="submit" id="qjump-submit" value="<?php echo $lang_common['Go'] ?>" /></span>
	</div>
</form>
<?php

$forum_javascript_quickjump_code = <<<EOL
(function () {
	var forum_quickjump_url = "http://massapokupok.ru/forum/forum/$1/$2/";
	var sef_friendly_url_array = new Array(6);
	sef_friendly_url_array[1] = "rabota-saita";
	sef_friendly_url_array[8] = "otkroite-zakupku";
	sef_friendly_url_array[2] = "zakupki";
	sef_friendly_url_array[4] = "boltalka-uchastnikov";
	sef_friendly_url_array[5] = "khvastiki";
	sef_friendly_url_array[7] = "predlozheniya-optovikov-organizatoram";

	PUNBB.common.addDOMReadyEvent(function () { PUNBB.common.attachQuickjumpRedirect(forum_quickjump_url, sef_friendly_url_array); });
}());
EOL;

$forum_loader->add_js($forum_javascript_quickjump_code, array('type' => 'inline', 'weight' => 60, 'group' => FORUM_JS_GROUP_SYSTEM));
?>

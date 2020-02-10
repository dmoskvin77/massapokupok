;<?php exit(); ?>
;
[framework]

; Каталог, где хранятся классы 
; контролов Controls (относительно каталога приложения)
directory.controls = APPLICATION_DIR "/Controls"

; Каталог, где хранятся классы 
; действий Actions (относительно каталога приложения)
directory.actions = APPLICATION_DIR "/Actions"

; Каталог, где хранятся шаблоны для 
; макетов страниц Layouts (относительно каталога приложения)
directory.layouts = APPLICATION_DIR "/Templates/layouts"

; Каталог, где хранятся шаблоны для 
; контролов (относительно каталога приложения)
directory.templates = APPLICATION_DIR "/Templates"

; Путь к файлу, в котором хранится список
; классов приложения и путей к ним
file.repository = APPLICATION_DIR "/repository.rep"

; Каталог, где хранятся классы 
; менеджеров данных
directory.managers = APPLICATION_DIR "/Managers"

; Каталог, где хранятся классы 
; сущностей
directory.entity = APPLICATION_DIR "/Entity"


[application]
name = "mp"					; Имя сессии НЕ МЕНЯТЬ!
url = "http://local-mpgit.ru"	; URL проекта (без конечного слеша)
urlHttps = "http://local-mpgit.ru"	; SSL-URL проекта (протокол https://, без конечного слеша)
internalIp = "http://127.0.0.1"		; Ip проекта относительно монеты
basePath = "/"						; Каталог, в котором находится приложение
nocache = true						; Отправлять HTTP заголовки запрещающие кеширование
encoding = "utf-8"					; Кодировка HTML содержимого
debug = true						; Режим отладки
protocol = "http://"				; Протокол
revisionFile = DOCUMENT_ROOT "/version.xml" ; XML файл, который содержит номер текущей ревизии
tempDir = DOCUMENT_ROOT "/app/var/tmp/"	; Каталог для временных файлов, например, для lock-файлов Mutex
productFolder = DOCUMENT_ROOT "/images/products/"
zhFolder = DOCUMENT_ROOT "/images/zheads/"
uphonesFolder = DOCUMENT_ROOT "/images/uphones/"
boardFolder = DOCUMENT_ROOT "/images/board/"
docFolder = DOCUMENT_ROOT "/storage/docs/"
hintsFolder = DOCUMENT_ROOT "/storage/hints/"
baseSiteId = 1
parserPrice = 500
connectw1Price = 500
paySystemCommisionPercent = 3			; процент при оплате через платежную систему
payPerOrderPrice = 1				; цена за один заказ (основному проекту)


;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;  Настройки админской части
;;;;;;;;;;;;;;;;;;;;;;;;;;;;
[adminka]
enableWYSIWYG = false; // отображать HTML редактор


[mail]
enabled = true
connection = "smtp"
from = "website@massapokupok.ru"
fromName = "SOVPOKUPKI.ru"
support = "website@massapokupok.ru"
sign = "С уважением SOVPOKUPKI.ru"
usleep = 500		; задержка между отправкой пачки писем
usend = 3			; кол-во писем между задержками
uall = 20			; кол-во писем за один крон

[smtp]
server = "mail.massapokupok.ru"
port = "25"
login = "website@massapokupok.ru"
password = "GFJrrXC8"

;;;;;;;;;;;;;;;;;;;;;;;;;;;;
; Используется репликация
;
; Настройки соединения к базе данных MASTER 
;;;;;;;;;;;;;;;;;;;;;;;;;;;;

[master]
debug = true
user = "root"
password = "1234"
host = "localhost"
database = "mp"
driver = "MySQL"
encoding = "utf8"
persist = false								; set persistent connection
port = 3306									; set another value if you need
;socket = "/var/run/mysqld/mysqld.sock"		; you may use socket

[vbforum]
debug = true
user = "root"
password = "1234"
host = "localhost"
database = "mpvbltnforum"
driver = "MySQL"
encoding = "utf8"
persist = false								; set persistent connection
port = 3306									; set another value if you need
;socket = "/var/run/mysqld/mysqld.sock"		; you may use socket
prefix = "mp_"


;;;;;;;;;;;;;;;;;;;;;;;;;;;;
; template system options
;;;;;;;;;;;;;;;;;;;;;;;;;;;;

[smarty]

; При каждом вызове РНР-приложения Smarty проверяет, изменился или нет текущий шаблон с момента 
; последней компиляции. Если шаблон изменился, он перекомпилируется. В случае, если шаблон еще не 
; был скомпилирован, его компиляция производится с игнорированием значения этого параметра. 
; По умолчанию эта переменная установлена в true. В момент, когда приложение начнет работать в реальных условиях 
;(шаблоны больше не будут изменяться), этап проверки компиляции становится ненужным.
compile.check = true


; Активирует debugging console - окно браузера, содержащее информацию о подключенных шаблонах 
; и загруженных переменных для текущей страницы. 
debugging = false


; Установка уровня ошибок, которые будут отображены. Соответствует уровням ошибок PHP
error.reporting = E_ALL & ~E_NOTICE


; Путь к каталогу для скомпилированных шаблонов
compile.dir = APPLICATION_DIR "/var/compile"


; Каталог для хранения конфигурационных файлов, используемых в шаблонах. 
; По умолчанию установлено в "./configs", т.е. поиск каталога с конфигурационными файлами 
; будет производиться в том же каталоге, в котором выполняется скрипт
config.dir = ""


; Имя каталога, в котором хранится кэш шаблонов. По умолчанию установлено в "./cache". 
; Это означает, что поиск каталога с кэшем будет производиться в том же каталоге, в котором 
; выполняется скрипт. Вы также можете использовать собственную функцию-обработчик для управления 
; файлами кэша, которая будет игнорировать этот параметр
cache.dir = APPLICATION_DIR "/var/cache"


; Это директория (или директории), в которых Smarty будет искать необходимые ему плагины. 
; По умолчанию это поддиректория "plugins" директории куда установлен Smarty. Если вы укажете относительный путь, 
; Smarty будет в первую очередь искать относительно SMARTY_DIR, затем оносительно текущей рабочей директории 
; (cwd, current working directory), а затем относительно каждой директории в PHP-директиве include_path. 
; Если $plugins_dir является массивом директорий, Smarty будет искать ваш плагин в каждой директории плагинов 
; в том порядке, в котором они указаны.
user.plugins = APPLICATION_DIR "/Smarty.Plugins"


; Настройки безопасности Smarty. Рекомендуется значение TRUE
security = true


; Это список имён PHP-функций, разрешенных к использованию в условиях IF. 
; Описанные здесь - добавляются к предопределенным. Должны быть разделены запятой
IF_FUNCS = "strpos"


; Это список имён PHP-функций, разрешенных к использованию в качестве модификаторов переменных.
; Описанные здесь - добавляются к предопределенным. Должны быть разделены запятой
MODIFIER_FUNCS = ""

;;;;;;;;;;;;;;;;;;;;;;;;;;;;
; Имена мутексов (префиксы)
;;;;;;;;;;;;;;;;;;;;;;;;;;;;
[mutex]
massmailsend = "massmailsend"
garbageremover = "garbageremover"
queueworker = "queueworker"
digestsend = "digestsend"
nightwork = "nightwork"
weekwork = "weekwork"

;;;;;;;;;;;;;;;;;;;;;;;;;;;;
; Logger options
;;;;;;;;;;;;;;;;;;;;;;;;;;;;

[logger]
logger.dir = DOCUMENT_ROOT "/app/var/logs/"

;;;;;;;;;;;;;;;;;;;;;;;;;;;;
; Настройка путей к шаблонам писем
;;;;;;;;;;;;;;;;;;;;;;;;;;;;

[MailTextHelper]
path = DOCUMENT_ROOT "/app/Templates/mail/"

;;;;;;;;;;;;;;;;;;;;;;;;;;;;
; Настройки журнала потенциальных опасностей
;;;;;;;;;;;;;;;;;;;;;;;;;;;;
[securitylog]
enabled = true ; включено\выключено
passwordBrutforceLimit = 3 ; количество попыток ввода пароля, которое считается подбором
evasiveLog = DOCUMENT_ROOT "/storage/evasive/" 	; Каталог для хранения логов mod_evasive (детектор большого числа запросов)

;;;;;;;;;;;;;;;;;;;;;;;;;;;;
; Настройки детектора  XSS, SQL-инъекций, инъекций заголовков, обхода каталогов, RFE/LFI, DoS и LDAP атак
;;;;;;;;;;;;;;;;;;;;;;;;;;;;
[IntrusionDetector]
enabled = true											; Включено\Выключено
rules = DOCUMENT_ROOT "/app/Config/intrusionRules.xml"	; Путь у XML файлу с правилами
stopOnFirstOccurence = false							; Прекращать проверку при первом же совпадении (для ускорения можно выставить true)
callback = "SecurityLogManager::writeIntrusionDetect"	; Имя метода для обработки события срабатывания правила

;;;;;;;;;;;;;;;;;;;;;;;;;;;;
; Настройки SMS Шлюза 
;;;;;;;;;;;;;;;;;;;;;;;;;;;;
[sms]
enabled = false ; вкл\выкл
login = "sovsms";
password = "pass123"


;;;;;;;;;;;;;;;;;;;;;;;;;;;;
[sphinx]
;;;;;;;;;;;;;;;;;;;;;;;;;;;;

enabled = true
server = 127.0.0.1
port = 3312
indexMask  = "indexLotomaniaMask"		; маски билетов

;<?php exit(); ?>

;;;;;;;;;;;;;;;;;;;;;;;;;;;;
; админка
;;;;;;;;;;;;;;;;;;;;;;;;;;;;

[createrole]
description = "Создание и редактирование роли"
inherit = "saveroleaction,rolelistcontrol,editrolecontrol,deleteroleaction"
group = "Управление персоналом"

[createoperators]
description = "Создание учетных записей операторов"
inherit = "operatorlistcontrol,editoperatorcontrol"
group = "Управление персоналом"

[editoperators]
description = "Редактирование учетных записей операторов"
inherit = "operatorlistcontrol,editoperatorcontrol,deleteoperatoraction,operatorresetpasswordaction"
group = "Управление персоналом"

[securitylog]
description = "Журнал потенциальных опасностей"
inherit = "securitylogcontrol"
group = "Получение отчетности"

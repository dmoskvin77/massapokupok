// MOD Title: Simple Image Upload
// MOD Author: Sium < admin@postimage.org > (N/A) http://postimage.org/
// MOD Version: 1.5.0
if (typeof postimage_lang === 'undefined') {
    var postimage_lang = "russian";
    var postimage_add_text = "Добавить картинку в сообщение";

    function postimage_query_string(postimage_search_name) 
    {
        if (window.location.hash) {
            postimage_query = window.location.hash.substring(1).split("&");
            for (postimage_i = 0; postimage_i < postimage_query.length; postimage_i++) {
                postimage_string_data = postimage_query[postimage_i].split("=");
                if (postimage_string_data[0] == postimage_search_name) {
                    postimage_string_data.shift();
                    return unescape(postimage_string_data.join("="));
                }
            }
        }
        return void(0);
    }

    // Вставка ссылки на картинку
    // Ссылку отдает внешний сервис
    if (opener) 
    {
        var postimage_text = postimage_query_string("postimage_text");
        if (postimage_text) 
	{
            var postimage_id = postimage_query_string("postimage_id");
            var postimage_area = opener.document.getElementsByTagName('textarea');
            for (var postimage_i = 0; postimage_i < postimage_area.length; postimage_i++) 
	    {
                if (postimage_i == postimage_id) 
		{
                    break;
                }
            }

            if (opener.editorHandlemessage && opener.editorHandlemessage.bRichTextEnabled) 
	    {
                opener.editorHandlemessage.insertText(postimage_text + "<br /><br />", false);
            }
	    else 
	    {
                postimage_area[postimage_i].value = postimage_area[postimage_i].value + postimage_text;
            }

            opener.focus();
            window.close();
        }
    }

    // Вставка ссылки "Добавить картинку в сообщение"
    function postimage_insert()
    {
        var postimage_area = document.getElementsByTagName('div');
        for (var postimage_i = 0; postimage_i < postimage_area.length; postimage_i++)
        {
            if (postimage_area[postimage_i].id.match(/adddescpics/i))
            {
                // TODO: надо ссылку вкорячить в линейку инструментов wysibb
                postimage_div = document.createElement('div');
                postimage_open = document.createElement('a');
                postimage_open.innerHTML = postimage_add_text;
                postimage_open.href = "javascript:postimage_upload(" + postimage_i + ");";

                postimage_div.appendChild(postimage_open);

                if (postimage_area[postimage_i].nextSibling) {
                    postimage_area[postimage_i].parentNode.insertBefore(postimage_div, postimage_area[postimage_i].nextSibling);
                } else {
                    postimage_area[postimage_i].parentNode.appendChild(postimage_div);
                }

            }
        }
    }

    function postimage_upload(areaid) {
        window.open("http://postimage.org/index.php?mode=website&areaid=" + areaid + "&hash=1&lang=" + postimage_lang + "&code=&content=family&forumurl=" + escape(document.location.href), "postimage", "resizable=yes,width=500,height=400");
        return void(0);
    }

    // Добавление ссылки "Добавить картинку в сообщение" с ожиданием события
    if (typeof postimage_text === 'undefined') {
        if (window.addEventListener) {
            window.addEventListener('DOMContentLoaded', postimage_insert, false);
        } else if (window.attachEvent) {
            window.attachEvent('onload', postimage_insert);
        }
    }

}
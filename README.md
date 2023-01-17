<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">Yii 2 Basic Project Template</h1>
    <br>
</p>

В корне прикреплена БД для задания.

** Конфиг db.php может отличаться

API запросы вополняются по следующим URL:

- запрос который выдает общее количество загруженных картинок 
    /web/api/get-data

- запрос с параметром указывающим запрошенную страницу в списке, возвращает список картинок по 10 штук на страницу
    /web/api/get-data?page=
    
- запрос c параметром id который вернет данные картинки по id {"id": 10, "path": "image.jpg"}
    /web/api/get-data?id=
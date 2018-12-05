# yandex-turbo-pages
### Yandex turbo pages generator using Yandex API

Отправляет в API произвольное количество страниц в виде XML-документов

Для использования API вам необходимо зарегистрироваться в сервисе Яндекс.Вебмастер и получить API-ключ для загрузки турбо-страниц.

### Пример использования
```
$turboApi = new TurboApi\TurboApi('адрес сайта', 'ваш токен', TurboApi::MODE_DEBUG);
$turboApi->requestUserId();
$turboApi->requestHost();
$turboApi->requestUploadAddress();

// лимит числа страниц в рамках одной задачи
$tasksLimit = 50;

$turboPack = new TurboApi\TurboPack('Заголовок сайта', 'URL сайта', 'Краткое описание сайта', 'Код языка сайта');

/*
	Предположим, у вас есть страницы категорий, у каждой - заголовок и описание
*/
foreach ($categories as $category) {
    $link = 'URL страницы категории';
    $taskItem = new TurboApi\TurboItem();

    $taskXML = '<item turbo="true"><link>' . $link . '</link>';
    $taskXML .= '<turbo:content><![CDATA[';
    $taskXML .= '<header>
               <h1>' . $category->seoH1 . '</h1>
           </header>';
    $taskXML .= $category->description;
    $taskXML .= ']]></turbo:content></item>' . PHP_EOL;

    $taskItem->setXml($taskXML);
    $turboPack->addItem($taskItem);
}

$tasks = $turboPack->getTasks($tasksLimit);

foreach ($tasks as $task) {
    $taskIds[] = $turboApi->uploadRss($task);
}
```

### Как задать данные сервера API
Если необходимо задать данные, отличные от данных по умолчанию (актуальны на 5.12.2018), задайте их через конструктор:
```
$turboApi = new TurboApi\TurboApi('адрес сайта', 'ваш токен', TurboApi::MODE_DEBUG, 'https://api.webmaster.yandex.net', 'v3.2');
```
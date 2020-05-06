# Клиент для API турбостраниц Яндекса

Отправляет в API произвольное количество страниц в виде XML-документов

Для использования API вам необходимо зарегистрироваться в сервисе Яндекс.Вебмастер и получить API-ключ для загрузки турбо-страниц.

Подробно о турбостраницах можно прочитать, например, здесь: https://webmaster.yandex.ru

### Установка
Установить пакет вы можете через Composer
```
composer require yurcrm/yandex-turbo-pages
```

### Пример использования
```
// Инициализируем клиент в режиме отладки. Для боевого режима укажите третьим параметром TurboApi::MODE_PRODUCTION
$turboApi = new TurboApi\TurboApi('адрес сайта', 'ваш токен', TurboApi::MODE_DEBUG);

// Получаем у Яндекса необходимые данные для отправки турбостраниц
$turboApi->requestUserId();
$turboApi->requestHost();
$turboApi->requestUploadAddress();

// лимит числа страниц в рамках одной задачи. В режиме дебага это число ограничено, см. документацию от Яндекса
$tasksLimit = 30;

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

// разбиваем массив турбостраниц на задачи
$tasks = $turboPack->getTasks($tasksLimit);

// В этом массиве будем хранить id задач, чтобы потом получать информацию по ним
$taskIds = [];

// отправляем задачи в Яндекс
foreach ($tasks as $task) {
    $taskIds[] = $turboApi->uploadRss($task);
}
```

### Как получить статус обработки задачи

Предположим, в предыдущем примере вы получили массив id задач от Яндекса. Получим статус по задаче $taskId
```php
// Инициализируем клиент в режиме отладки. Для боевого режима укажите третьим параметром TurboApi::MODE_PRODUCTION
$turboApi = new TurboApi\TurboApi('адрес сайта', 'ваш токен', TurboApi::MODE_DEBUG);

// Получаем у Яндекса необходимые данные для отправки турбостраниц
$turboApi->requestUserId();
$turboApi->requestHost();
$turboApi->requestUploadAddress();
$status = $turboApi->getTask($taskId);
```
### Как получить список задач
```php
// Инициализируем клиент в режиме отладки. Для боевого режима укажите третьим параметром TurboApi::MODE_PRODUCTION
$turboApi = new TurboApi\TurboApi('адрес сайта', 'ваш токен', TurboApi::MODE_DEBUG);

// Получаем у Яндекса необходимые данные
$offset = 0; // Смещение в списке. Минимальное значение — 0
$limit = 5; // Ограничение на количество элементов в списке. Минимальное значение — 1; максимальное значение — 100.
$taskTypeFilter = TurboApi::TASK_TYPE_FILTER_DEBUG; // Фильтрация по режиму загрузки RSS-канала. Возможные значения: DEBUG, PRODUCTION, ALL.
$loadStatusFilter = TurboApi::LOAD_STATUS_FILTER_PROCESSING; // Фильтрация по статусу загрузки RSS-канала. Возможные значения: PROCESSING, OK, WARNING, ERROR.
$status = $turboApi->getTasks($offset, $limit, $taskTypeFilter, $loadStatusFilter);
```
### Как задать данные сервера API
Если необходимо задать данные, отличные от данных по умолчанию (актуальны на 5.12.2018), задайте их через конструктор:
```
$turboApi = new TurboApi\TurboApi('адрес сайта', 'ваш токен', TurboApi::MODE_DEBUG, 'https://api.webmaster.yandex.net', 'v3.2');
```

### Планы развития
В будущих версиях появится возможность просматривать более детальные ответы от Яндекса, а не только id задач и статусы.
<?php
/**
 * Основной файл запуска программы.
 */
spl_autoload_register();

$config = require_once('config.php');
$dataView = [];
$view = new View();

try{
    $contacts = new Contact($config['mysql']);

    // Запрос пришел через AJAX
    if(!empty($_POST['action'])){
        $post = new Post($_POST, $contacts);
        echo $view->post( $post->getMessage() );
    }else{
        $dataView = $contacts->getContacts();

        if($dataView == [])
            $dataView = ['message' => ['type' => 'info', 'mess' => Contact::E_EMPTY]];
        
        echo $view->view($dataView);
    }
}
catch(PDOException $e){
    // Это ошибка при вставке дубликата, в поля помеченные как уникальные.
    // Данную проверку можно вынести отдельно.
    // Но т.к. ТЗ четко не регламентирует, что делать с дублями: запретить, создать копию, перейти в режим редактирования и т.д.
    // То просто ловим тут ошибку и сообщаем про нее.
    if($e->getCode() == '23000')
        echo json_encode(["status" => false, "data" => "", "mess" => Model::E_DUPLICATE]);
    else{
        $dataView['message'] = ['type' => 'danger', 'mess' => Model::E_DB];
        file_put_contents('error.log',$e->getMessage()."\r\n", FILE_APPEND);
    }
}
catch(Exception $e){
    $dataView['message'] = ['type' => 'danger', 'mess' => 'Что-то пошло не так...'];
    file_put_contents('error.log',$e->getMessage()."\r\n", FILE_APPEND);
}
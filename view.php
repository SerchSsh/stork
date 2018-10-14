<?php

/**
 * Класс для отрисовки страницы.
 */
class View{
    // HTML-шаблон для вывода информации при пустой базе и ошибок
    private const E_MESS = '<div class="alert alert-{class}" role="alert">{error}</div>';
    // Шаблон для заголовка таблицы данных. Можно поместить и в отдельный файл-шаблона.
    private const CONTACT_HEADER = '<div class="row info">
                                        <div class="col">ФИО</div>
                                        <div class="col">Телефон</div>
                                        <div class="col">Email</div>
                                        <div class="col">Действия</div>
                                    </div>';
    // Переменны-шаблоны для формирования страницы
    private $header;
    private $footer;
    // Переменная-шаблона для формирования строки таблицы.
    private $userRowTpl;

    /**
     * Конструктор.
     *
     */
    function __construct(){
        $this->setTpl();
    }

    /**
     * Готовим вывод страницы
     *
     * @param array $params - массив параметров, может содержать сообщение или список контактов.
     * @return string - Шаблон страницы.
     */
    public function view(array $params): string{
        $output = $this->header;
        // Если есть сообщение то формируем его, для вывода пользователю. Иначе формируем список контактов.
        $output .= !isset($params['message']) 
                    ? $this->getContactList($params) 
                    : str_replace(['{class}','{error}'], $params['message'], View::E_MESS);

        $output .= $this->footer;

        return $output;
    }

    /**
     * Готовим ответ данных для post-запросов.
     * Решил расположить в данном классе, так как html-список собирается на сервере в данном классе, а на клиенте идет простая вставка.
     *
     * @param array $params - список контактов.
     * @return string - JSON-строка. Содержит подготовленный html-список контактов и статус обработки запроса.
     */
    public function post(array $params):string{
        if($params['data'] == [])
            return json_encode(["status" => $params['status'], "data" => str_replace(['{class}','{error}'], ['info', Contact::E_EMPTY], View::E_MESS) ]);
        $data = $this->getContactList($params['data']);
        return json_encode(["status" => $params['status'], "data" => $data]);
    }

    /**
     * Подготавливаем вывод блока с контактами
     *
     * @param array $users - массив со списком контактов.
     * @return string - блок с контактами, в виде строки. Готовый к вставке в шаблон. Либо пустую строку при пустом массиве контактов.
     */
    private function getContactList(array $users): string{
        if(empty($users))
            return '';
        
        $listTpl = View::CONTACT_HEADER;

        foreach($users as $value)
            $listTpl .= str_replace(array('{{name}}', '{{phone}}', '{{email}}', '{{id}}'), array($value['name'], $this->getPhone($value['phone']), $value['email'], $value['id'] ?? ''), $this->userRowTpl);

        return $listTpl;
    }

    /**
     * Подготовим удобный для чтения формат телефона.
     * Можно написать отдельный метод для формирования телефона по шаблону, но в рамках теста и отсутствия договоренности по допустимым форматам телефона,
     * Решил сделать для примера два варианта.
     *
     * @param string $phone - номер телефона, состоящий только из цифр.
     * @return string - номер телефона в отформатированном виде.
     */
    private function getPhone(string $phone):string{
        switch(strlen($phone)){
            case 6: return substr($phone, 0, 3).'-'.substr($phone,3);
            case 11: return $phone[0].' ('.substr($phone, 1, 3).') '.substr($phone, 4, 3).'-'.substr($phone, 7, 2).'-'.substr($phone, 9);
        }
        return $phone;
    }

    /**
     * Установим шаблоны для различных элементов.
     *
     * @return void
     */
    private function setTpl(){
        $this->header = file_get_contents('views/header.tpl');
        $this->footer = file_get_contents('views/footer.tpl');
        $this->userRowTpl = file_get_contents('views/user_row.tpl');
    }
}
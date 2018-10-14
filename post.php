<?php
/**
 * Класс для обработки запросов (в данном случае AJAX) с клиента.
 * Специальная проверка на тип запроса (AJAX), в рамках теста, опущена.
 */
class Post{

    // Данные с клиента.
    private $data;
    // Тип запроса = действие.
    private $type;
    // Экземпляр класса Contact.
    private $contact;

    /**
     * Конструктор.
     *
     * @param array $data - данные с клиента.
     * @param Contact $contact - объект класса Contact.
     */
    function __construct(array $data, Contact $contact){
        $this->data = $data;
        $this->contact = $contact;
        $this->getType();
    }

    /**
     * Установим тип запроса.
     *
     * @return void
     */
    private function getType():void{
        if( !empty($this->data['search']) )
            $this->type = 'search';
        else if( isset($this->data['cid']) )
            $this->type = !empty($this->data['delete']) ? 'delete' : 'action';
        else
            $this->type = 'all';
    }

    /**
     * Формируем "сырой" ответ.
     *
     * @return array - содержит статус выполнения и возможно, данные о контактах.
     */
    public function getMessage():array{
        $status = false;
        $output = [];
        switch($this->type){
            case 'search':
                if($this->validate('search')){
                    $searchResult = $this->contact->searchContact($this->data['search']);
                    $output['data'] = $searchResult;
                    $status = true;
                }

                break;
            case 'action':
                if($this->validateAll()){
                    $status = true;
                    $query = ( $this->data['cid'] == '0' ) ? $this->contact->insert($this->data) : $this->contact->update($this->data);
                    if($query)
                        $output['data'] = $this->contact->getContacts();
                }
                break;
            case 'delete':
                if($this->validate('cid') && $this->data['cid'] != '0')
                    if($this->contact->delete(intval($this->data['cid']))){
                        $status = true;
                        $output['data'] = $this->contact->getContacts();
                    }
                break;
            // Вывод всех контактов. Актуально при сбросе результатов поиска.
            case 'all':
                    $status = true;
                    $output['data'] = $this->contact->getContacts();
                    break;
        }
        return array_merge(["status" => $status], $output);
    }

    /**
     * Проверяем все данные с клиента.
     *
     * @return boolean - флаг, пройдена проверка или нет.
     */
    private function validateAll():bool{
        foreach($this->data as $key =>$value)
            if(!$this->validate($key))
                return false;   
        return true;
    }

    /**
     * Проверка одного поля данных.
     *
     * @param string $type - строка-ключ поля.
     * @return boolean - флаг, пройдена проверка или нет.
     */
    private function validate(string $type):bool{
        if(!isset($this->data[$type]))
            return false;
        $validate = true;
        // Для поиска и email'а делаем проверку на соответствие шаблону, т.к. не точно понятно, что пользователь имеет ввиду.
        // Для телефона и имени, ну и для ID, проверка равна удалению, всего "ненужного". Предполагается, что имя должно быть только из слов и разделителей ввиде пробелов.
        switch($type){
            case 'search':
                $validate = filter_var($this->data[$type], FILTER_VALIDATE_REGEXP, array('options'=>array('regexp'=>'/^([\w\s])+$/u')));
                break;
            case 'cid':
            case 'inputPhone':
                $this->data[$type] = preg_replace('/[^\d]/ui', '', $this->data[$type]);
                $len = strlen(trim($this->data[$type]));
                $validate = ( $len > 0 && $len < 12 ) ? true : false;
                break;
            case 'inputName':
                $this->data[$type] = trim(preg_replace(['/[^a-zа-яё^\s]+/ui', '/\s{2,}/ui'], ['', ' '], $this->data[$type]));
                $validate = ( $this->data[$type] != '' ) ? true : false;
                break;
            case 'inputEmail':
                $validate = filter_var($this->data[$type], FILTER_VALIDATE_EMAIL);
                break;
        }

        return $validate;
    }
}
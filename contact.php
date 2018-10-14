<?php
/**
 * Класс-модель для работы с таблицей контактов.
 * Расширяем базовый класс-модель.
 */
class Contact extends Model{
    // Константы содержат шаблоны sql-запросов.
    private const GET_ALL_CONTACTS = 'SELECT * FROM contacts ORDER BY name';
    private const UPDATE_CONTACT = 'UPDATE contacts SET name=?, phone=?, email=? WHERE id=?';
    private const INSERT_CONTACT = 'INSERT INTO contacts (id, name, phone, email) VALUES (NULL, ?, ?, ?)';
    private const DELETE_CONTACT = 'DELETE FROM contacts WHERE id=?';
    private const SEARCH_CONTACT = 'SELECT * FROM contacts WHERE name LIKE ?';
    // Сообщение, когда контактов нет.
    public const E_EMPTY = 'У вас нет записей.';

    private $contacts;

    /**
     * Получаем список всех контактов.
     *
     * @return array - Массив со списком контактактов, либо пустой массив.
     */
    public function getContacts():array{
        $list = $this->db->query(Contact::GET_ALL_CONTACTS);
        return $list->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Проводим поиск по имени
     *
     * @param string $param - строка поиска.
     * @return array - массив контактов удовлетворяющих условию поиска. Либо же пустой массив.
     */    
    public function searchContact(string $param):array{
        $sth = $this->db->prepare(Contact::SEARCH_CONTACT);
        $sth->execute(["%{$param}%"]);
        
        return $sth->fetchAll();
    }

    /**
     * Вставляем данные в базу.
     * Три метода: вставка, обновление и удаление, по сути одинаковы, с разными запросами к БД. Ну и удаление принимает только числовой id, а не массив.
     * Можно было бы и в единственном, универсальном методе сделать, с разделение по условию.
     * Но решил вынести отдельно в методы.
     *
     * @param array $data - данные о контакте.
     * @return boolean - статус о выполнении операции запроса.
     */
    public function insert(array $data):bool{
        $sth = $this->db->prepare(Contact::INSERT_CONTACT);
        return $sth->execute([$data['inputName'], $data['inputPhone'], $data['inputEmail']]);
    }

    public function update(array $data):bool{
        $sth = $this->db->prepare(Contact::UPDATE_CONTACT);
        return $sth->execute([$data['inputName'], $data['inputPhone'], $data['inputEmail'], $data['cid']]);
    }

    public function delete(int $data):bool{
        $sth = $this->db->prepare(Contact::DELETE_CONTACT);
        return $sth->execute([$data]);
    }
}
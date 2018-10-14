$(()=>{
    
    // События для кнопок "Добавить пользователя" и "Редактировать"
    $('body').on('click', ' #addUser, .updateUser', e=>{
        // Если обновляем информацию о пользователе, тогда установим данный в модальном окне.
        if($(e.target).attr('id') != 'addUser'){
            setModal($(e.target).closest('.row'));
        }
        $('#userModal').modal('show');
    });

    // События для форм, с данными пользователя, для удаления, для сброса результатов поиска и для поиска.
    $('body').on('click', '#goForm, #searchGo, #showAll, .deleteUser', function(){
        // Формируем данные отправляемые на сервер, в зависимости от типа запроса.
        switch($(this).attr('id')){
            case 'goForm':  if($('#inputName').val() == '' || $('#inputPhone').val() == '' || $('#inputEmail').val() == '') return 0;
                            var data = $('#userForm').serialize();
                            break;
            case 'searchGo':if($('#search').val() == '') return 0;
                            data = 'search='+$('#search').val();
                            break;
            case 'showAll':data = 'all=1';
                            break;
            default: 
                if($(this).hasClass('deleteUser'))
                    data = 'cid='+$(this).attr('data-id')+'&delete=1';
                else
                    return 0;
        }
        
        $.ajax({
            method:'POST',
            data:data+'&action=1'
        }).done(resp=>{
            data = JSON.parse(resp);
            if(data.status){
                if(data.data == '')
                    alert('Поиск результатов не дал.');
                else
                    addRowUsers(data.data);
                $('#userModal').modal('hide');
            }else{
                if(data.mess != undefined && data.mess != '')
                    alert(data.mess);
                else
                    alert('Проверьте корректность введенных данных.');
            }
        });
    });

    // Очищаем модальное окно при закрытии
    $('#userModal').on('hidden.bs.modal', e=>{
        clearModal();
    });
});

/**
 * Установим данные пользователя в форму для редактирования.
 * 
 * @param {Элемент линии с редактируемым пользователям.} el 
 */
function setModal(el){
    console.log(el);
    var elms = $(el).children();
    $('#inputName').val($(elms).eq(0).text().trim());
    $('#inputPhone').val($(elms).eq(1).text().trim());
    $('#inputEmail').val($(elms).eq(2).text().trim());
    $('#cid').val($(el).find('button').attr('data-id'));
}

/**
 * Очистка модального окна
 */
function clearModal(){
    $('#inputName').val('');
    $('#inputPhone').val('');
    $('#inputEmail').val('');
    $('#cid').val(0);
}

/**
 * Добавляем пользователя(лей) в таблицу на странице.
 * @param {Массив с данными об измененном или добавленном пользователе} data 
 */
function addRowUsers(data){
    var newData = $(data);
    // Так как нам приходит уже подготовленный блок с пользователями, то при необходимости удаляем текущий.
    if($('.container').length > 0)
        $('.container').children().remove();

    $('.container').prepend(newData);
}
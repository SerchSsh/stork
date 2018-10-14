</div>
<div class="modal" tabindex="-1" role="dialog" id="userModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Добавить контакт</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="userForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="inputName">Имя</label>
                        <input type="text" class="form-control" name="inputName" id="inputName" aria-describedby="fioHelp" placeholder="Укажите Имя" />
                    </div>
                    <div class="form-group">
                        <label for="inputPhone">Телефон</label>
                        <input type="tel" class="form-control" id="inputPhone" name="inputPhone" placeholder="8 (123) 456-78-90" value="" />
                    </div>
                    <div class="form-group">
                        <label for="inputEmail">Email</label>
                        <input type="email" class="form-control" id="inputEmail" name="inputEmail" placeholder="email@example.com" value="" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                    <button type="button" class="btn btn-primary" id="goForm">Сохранить</button>
                </div>
                <input type="hidden" name="cid" id="cid" value="0" />
            </form>
        </div>
    </div>
</div>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="views/js/main.js"></script>
</body>
</html>
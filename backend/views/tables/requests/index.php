<script type="text/javascript" src="/profile/files/js/mytables/requests/index.js"></script>
<script type="text/javascript" src="/profile/files/js/plugins/notifications/sweet_alert.min.js"></script>

<?=$this->render("/layouts/header/_header")?>

<div class="content">
    <div class="row">
        <div class="col-sm-12 col-lg-12">
            <div class="panel">
                <div class="panel-body">
                    <?=$this->render('/layouts/header/_filter', array('page' => $page))?>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Имя водителя</th>
                            <th>Телефон</th>
                            <th>Сумма</th>
                            <th>Баланс водителя</th>
                            <th>Номер кредитной карты</th>
                            <th>Таксопарк водителя</th>
                            <th>Дата создания заявки</th>

                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="/profile/files/js/mytables/moderators/index.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
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
                            <th></th>
                            <th>ID</th>
                            <th>Модератор</th>
                            <th>email</th>
                            <th>Регион</th>
                            <th>Количество водителей</th>
                            <th>Количество клиентов</th>
                            <th>Монеты пополненные вручную</th>
                            <th>Сумма сданная в кассу</th>
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


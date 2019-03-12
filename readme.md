RESTFull API
=================
api post 'wallet/' //Создание кошелька
api delete 'wallet/{wallet_id}' //Удаление кошелька (protected)

api post 'wallet/payment/in/{wallet_id}/{coins}' //Пополнение счета (protected)
api post 'wallet/payment/out/{wallet_id}/{coins}' //Вывод средств (protected)

api post 'wallet/delegate/{wallet_id}/{server_id}/{coins}' //Делегирование (protected)
api post 'wallet/undelegate/{wallet_id}/{server_id}/{coins}' //Анделигирование (protected)

api post 'wallet/reward/{wallet_id}/{server_id}/{coins}' //Награда

api post 'servers/{address}/{server_name}' //Создание сервера
api delete 'servers/{server_id}' //Удаление сервера

api get 'wallet/'  //Получение списка кошелька пользователя
api get 'wallet/{wallet_id}/transaction/' //Получение транзакций кошелька
api post 'wallet/transaction/'
api get 'servers/' //Получение списка серверов

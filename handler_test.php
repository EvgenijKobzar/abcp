<?php
include($_SERVER["DOCUMENT_ROOT"].'/abcp-main/Entity/Contractor.php');
include($_SERVER["DOCUMENT_ROOT"].'/abcp-main/Entity/Client.php');
include($_SERVER["DOCUMENT_ROOT"].'/abcp-main/Entity/Employee.php');
include($_SERVER["DOCUMENT_ROOT"].'/abcp-main/Entity/EntityContractorFactory.php');
include($_SERVER["DOCUMENT_ROOT"].'/abcp-main/Entity/Position.php');
include($_SERVER["DOCUMENT_ROOT"].'/abcp-main/Entity/Seller.php');
include($_SERVER["DOCUMENT_ROOT"].'/abcp-main/Enum/CommunicationType.php');
include($_SERVER["DOCUMENT_ROOT"].'/abcp-main/Enum/EntityType.php');
include($_SERVER["DOCUMENT_ROOT"].'/abcp-main/Enum/NotificationType.php');
include($_SERVER["DOCUMENT_ROOT"].'/abcp-main/Enum/StatusType.php');
include($_SERVER["DOCUMENT_ROOT"].'/abcp-main/Lib/Communication/Manager.php');
include($_SERVER["DOCUMENT_ROOT"].'/abcp-main/Lib/Env/Vars.php');
include($_SERVER["DOCUMENT_ROOT"].'/abcp-main/Lib/Handler/HandlerReturn.php');
include($_SERVER["DOCUMENT_ROOT"].'/abcp-main/Lib/Error.php');
include($_SERVER["DOCUMENT_ROOT"].'/abcp-main/Lib/Result.php');

$_REQUEST['data'] = [
    'clientId'=> 101,
    'expertId'=> 32,
    'creatorId'=> 20,
    'resellerId' => 2,
    'notificationType'=> 1,
    'differences' => [
        'from' => 1,
        'to' => 2
    ],
    'complaintId' => 1001,
    'complaintNumber' => '25/01/2024-20',
    'consumptionId' => 151,
    'consumptionNumber' => 'C-25/01/2024-20',
    'agreementNumber' => 'A-102',
    'date' => '2024-01-25T12:19:21+00:00',
    'hash' => '9QinSUmAqX8y6u77H9Xq4G2nC1G/LHGKOcTeh/4Vsi8=',
    'expired' => 1708851112 // Sun Feb 25 2024 08:51:52 GMT+0000
];

$r = (new \Lib\Handler\HandlerReturn())
    ->process();

echo '<pre>';
$r->isSuccess()
    ? print_r($r->getData()['employees'])
    : print_r($r->getErrorsMessage())
    ;

/*
 *
 Array
(
    [0] => Array
        (
            [emailFrom] => Seller_2@mail.com
            [emailTo] => Employee_201@mail.com
            [subject] => subject
            [message] => message - {"COMPLAINT_ID":1001,"COMPLAINT_NUMBER":"25\/01\/2024-20","CREATOR_ID":20,"CREATOR_NAME":"Employee_20 20","EXPERT_ID":32,"EXPERT_NAME":"Employee_32 32","CLIENT_ID":101,"CLIENT_NAME":"Client_101 101","CONSUMPTION_ID":151,"CONSUMPTION_NUMBER":"C-25\/01\/2024-20","AGREEMENT_NUMBER":"A-102","DATE":"2024-01-25T12:19:21+00:00","DIFFERENCES":{"from":1,"to":2}}
        )

    [1] => Array
        (
            [emailFrom] => Seller_2@mail.com
            [emailTo] => Employee_202@mail.com
            [subject] => subject
            [message] => message - {"COMPLAINT_ID":1001,"COMPLAINT_NUMBER":"25\/01\/2024-20","CREATOR_ID":20,"CREATOR_NAME":"Employee_20 20","EXPERT_ID":32,"EXPERT_NAME":"Employee_32 32","CLIENT_ID":101,"CLIENT_NAME":"Client_101 101","CONSUMPTION_ID":151,"CONSUMPTION_NUMBER":"C-25\/01\/2024-20","AGREEMENT_NUMBER":"A-102","DATE":"2024-01-25T12:19:21+00:00","DIFFERENCES":{"from":1,"to":2}}
        )

    [2] => Array
        (
            [emailFrom] => Seller_2@mail.com
            [emailTo] => Employee_203@mail.com
            [subject] => subject
            [message] => message - {"COMPLAINT_ID":1001,"COMPLAINT_NUMBER":"25\/01\/2024-20","CREATOR_ID":20,"CREATOR_NAME":"Employee_20 20","EXPERT_ID":32,"EXPERT_NAME":"Employee_32 32","CLIENT_ID":101,"CLIENT_NAME":"Client_101 101","CONSUMPTION_ID":151,"CONSUMPTION_NUMBER":"C-25\/01\/2024-20","AGREEMENT_NUMBER":"A-102","DATE":"2024-01-25T12:19:21+00:00","DIFFERENCES":{"from":1,"to":2}}
        )

    [3] => Array
        (
            [emailFrom] => Seller_2@mail.com
            [emailTo] => Employee_204@mail.com
            [subject] => subject
            [message] => message - {"COMPLAINT_ID":1001,"COMPLAINT_NUMBER":"25\/01\/2024-20","CREATOR_ID":20,"CREATOR_NAME":"Employee_20 20","EXPERT_ID":32,"EXPERT_NAME":"Employee_32 32","CLIENT_ID":101,"CLIENT_NAME":"Client_101 101","CONSUMPTION_ID":151,"CONSUMPTION_NUMBER":"C-25\/01\/2024-20","AGREEMENT_NUMBER":"A-102","DATE":"2024-01-25T12:19:21+00:00","DIFFERENCES":{"from":1,"to":2}}
        )

)
 * */
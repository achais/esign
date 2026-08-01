

<h1 align="center"> ESign </h1>

<p align="center">A PHP package developed based on the official eSign documentation, Wukong API V2 SDK.</p>


## Installation

```shell
$ composer require achais/esign:dev-master -vvv
```

## Usage

```php
// Configuration
$config = [
   'debug' => true, // Enable debug mode
   'app_id' => "****", // Replace with your own AppId
   'secret' => '****', // Replace with your own Secret
   'production' => false, // Use production environment

   'log' => [
       'level'      => 'debug',
       'permission' => 0777,
       'file'       => '/tmp/esign.log', // Effective when debug is enabled, specify the log file path
   ],
];

$eSign = new \Achais\ESign\Application($config);

$thirdPartyUserId = 'your_party_user_id'; // Unique user identifier. You can pass a third-party platform's user ID, ID number, phone number, email, etc. If set, it serves as a unique account field, and duplicate accounts with the same information cannot be created.
$name = 'your_name'; // Name
$idType = 'CRED_PSN_CH_IDCARD'; // ID type
$idNumber = 'your_id_number'; // ID number
$mobile = 'your_mobile'; // Phone number. The corresponding signatory will receive an SMS notification when the signing process starts.
$email = 'your_email'; // Email address. The corresponding signatory will receive an email notification when the signing process starts.

// Create personal account. Requires a unique identifier. You must record the returned accountId.
$accountInfo = $eSign->account->createPersonAccount($thirdPartyUserId, $name, $idType, $idNumber, $mobile, $email);
$accountId = $accountInfo['accountId'];

// Test contract template ID
$templateId = 'd895b34de77041dca853aa454c042cb2';

// Variables to fill the test contract template
$simpleFormFields = [
    '1a54591dcb5f40bb86048743e7e21c18' => 'Test Name',
    '9b55340f5a7a4b089dd7c03a397fa4ef' => 'Test Party A',
    'c7efd37736a94e1c85ffb21fd0de88ff' => date('Y-m-d'),
];

// Create document based on template
$fileInfo = $eSign->file->createByTemplateId($templateId, 'Lease Agreement', $simpleFormFields);
$fileId = $fileInfo['fileId'];

// Create a signing process
$flowInfo = $eSign->signflow->createSignFlow("Lease Agreement");
$flowId = $flowInfo['flowId'];

// Add document to the signing process
$addDocRet = $eSign->signflow->addDocuments($flowId, $fileId);

// Add a manual signing area to the signing process. Prerequisites: document already added to the process, and specify the signatory's accountId.
$handSignData = $eSign->signflow->addHandSign($flowId, $fileId, $accountId, 1, 100, 100);

// Start the signing process. The signatory will receive a notification (provided mobile/email is set).
$startSignFlowRet = $eSign->signflow->startSignFlow($flowId);
echo $startSignFlowRet;
```

## More Methods

`\Achais\ESign\Application` provides several services listed in the `$providers` property. For detailed usage instructions, it's best to read the source code directly~ I'm being a bit lazy here.

View methods and parameters with quick jump links:
- AccessToken related [Click here to view](https://github.com/achais/esign/tree/master/src/Core/AccessToken.php)
- Signatory Account API related [Click here to view](https://github.com/achais/esign/tree/master/src/Account/Account.php)
- File & Template API related [Click here to view](https://github.com/achais/esign/tree/master/src/File/File.php)
- Signing Process API related [Click here to view](https://github.com/achais/esign/tree/master/src/SignFlow/SignFlow.php)


## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/achais/esign/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/achais/esign/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## License

MIT

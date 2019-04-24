<?php
require_once 'vendor/autoload.php';
require_once "./random_string.php";

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

$connectionString = "DefaultEndpointsProtocol=https;AccountName=ferdystorage;AccountKey=8V70e+tE4zhy9NQ6XT+V8KAHPjDHlr5f0D6vHYi6NJUOGO4Iqi3saVqArFqMMAL96m1uzTpFaSh7EwCDcr2bBw==";
$containerName1 = "blobferdy";
// Create blob client.
$blobClient = BlobRestProxy::createBlobService($connectionString);
if (isset($_POST['submit'])) {
	$createContainerOptions = new CreateContainerOptions();
	$containerName = "blobferdy".generateRandomString();
	$blobClient->createContainer($containerName, $createContainerOptions);
	
   	$fileToUpload = strtolower($_FILES["fileToUpload"]["name"]);
   	$content = fopen($_FILES["fileToUpload"]["tmp_name"], "r");
   	$blobClient->createBlockBlob($containerName, $fileToUpload, $content);
	header("Location: index.php");
	
	
}
$listBlobsOptions = new ListBlobsOptions();
$listBlobsOptions->setPrefix("");
$result = $blobClient->listBlobs($containerName1, $listBlobsOptions);
?>

<!DOCTYPE html>
<html>
 <head>
 <Title>Submission 2</Title>
 <style type="text/css">
 	body { background-color: #fff; border-top: solid 10px #000;
 	    color: #333; font-size: .85em; margin: 20; padding: 20;
 	    font-family: "Segoe UI", Verdana, Helvetica, Sans-Serif;
 	}
 	h1, h2, h3,{ color: #000; margin-bottom: 0; padding-bottom: 0; }
 	h1 { font-size: 2em; }
 	h2 { font-size: 1.75em; }
 	h3 { font-size: 1.2em; }
 	table { margin-top: 0.75em; }
 	th { font-size: 1.2em; text-align: left; border: none; padding-left: 0; }
 	td { padding: 0.25em 2em 0.25em 0em; border: 0 none; }
 </style>
 </head>
 <body>
 <h1>Upload here!</h1>
 <form method="post" action="index.php" enctype="multipart/form-data" >
       Pilih File  <input type="file" name="fileToUpload" accept=".jpeg,.jpg,.png" required=""></br></br>
       <input type="submit" name="submit" value="Upload">
 </form>
 
 <table class='table table-hover'>
	<thead>
	   <tr>
	      <th>File Name</th>
	      <th>URL</th>
	      <th>Action</th>
	   </tr>
	</thead>
	<tbody>
	   <?php
	   do {
              foreach ($result->getBlobs() as $blob)
	      {
		?>
		<tr>
		   <td><?php echo $blob->getName() ?></td>
		   <td><?php echo $blob->getUrl() ?></td>
		   <td>
		      <form action="computervision.php" method="post">
			<input type="hidden" name="url" value="<?php echo $blob->getUrl()?>">
			<input type="submit" name="submit" value="Analyze">
		      </form>
		   </td>
		</tr>
		<?php
	      }
	      $listBlobsOptions->setContinuationToken($result->getContinuationToken());
	   } while($result->getContinuationToken());
	   ?>
	</tbody>
	</table>
 </body>
 </html>

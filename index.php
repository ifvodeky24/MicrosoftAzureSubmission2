<?php

require_once 'vendor/autoload.php';
require_once "./random_string.php";

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

	$connectionString = "DefaultEndpointsProtocol=https;AccountName=idwprojectstorage;AccountKey=lKJ9a/IDdfZ/Sq4miv6z14ENjM3vkUASoGzWru2gX62vF47LM63T5WhgxpuEJB4J1m5bLoXpD1aMeofomdr/hg==;EndpointSuffix=core.windows.net";

	// Create blob client.
	$blobClient = BlobRestProxy::createBlobService($connectionString);

    $containerName = "gambar";

    if (isset($_POST['submit'])) {
    $fileToUpload = $_FILES["fileToUpload"]["name"];
    $content = fopen($_FILES["fileToUpload"]["tmp_name"], "r");
    echo fread($content, filesize($fileToUpload));
        
    $blobClient->createBlockBlob($containerName, $fileToUpload, $content);
    header("Location: index.php");
}   

$listBlobsOptions = new ListBlobsOptions();
$listBlobsOptions->setPrefix("");
$result = $blobClient->listBlobs($containerName, $listBlobsOptions);
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Pengenalan Gambar</title>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>

        
        
    </head>
    
    <body>

            Upload Gambar yang akan dianalisa:
                    <form action="index.php" method="post" enctype="multipart/form-data">
                        <input type="file" name="fileToUpload" accept=".jpeg,.jpg,.png" required="" >
                        <input type="submit" name="submit" value="Upload">
                    </form>
            
                <br>

            <table>
                <tr>
                    <th>Nama Berkas Gambar</th>
                    <th>URL Gambar</th>
                </tr>
        
                <tbody>
                        <?php
                        do {
                            foreach ($result->getBlobs() as $blob) {
                        ?>                      
                        <tr>
                            <td><?php echo $blob->getName() ?></td>
                            <td><?php echo $blob->getUrl() ?></td>
                         
                        </tr>
                        <?php
                            } $listBlobsOptions->setContinuationToken($result->getContinuationToken());
                        } while($result->getContinuationToken());
                        ?>
                </tbody>    
            </table>

                <br>

   <script type="text/javascript">
        function processImage() {
            // **********************************************
            // *** Update or verify the following values. ***
            // **********************************************

            // Replace <Subscription Key> with your valid subscription key.
            var subscriptionKey = "8fc40a5a10ee476193bf65ebf2451a1a";

            // You must use the same Azure region in your REST API method as you used to
            // get your subscription keys. For example, if you got your subscription keys
            // from the West US region, replace "westcentralus" in the URL
            // below with "westus".
            //
            // Free trial subscription keys are generated in the "westus" region.
            // If you use a free trial subscription key, you shouldn't need to change
            // this region.
            var uriBase =
                "https://southeastasia.api.cognitive.microsoft.com/vision/v2.0/analyze";

            // Request parameters.
            var params = {
                "visualFeatures": "Categories,Description,Color",
                "details": "",
                "language": "en",
            };

            // Display the image.
            var sourceImageUrl = document.getElementById("inputImage").value;
            document.querySelector("#sourceImage").src = sourceImageUrl;

            // Make the REST API call.
            $.ajax({
                    url: uriBase + "?" + $.param(params),

                    // Request headers.
                    beforeSend: function (xhrObj) {
                        xhrObj.setRequestHeader("Content-Type", "application/json");
                        xhrObj.setRequestHeader(
                            "Ocp-Apim-Subscription-Key", subscriptionKey);
                    },

                    type: "POST",

                    // Request body.
                    data: '{"url": ' + '"' + sourceImageUrl + '"}',
                })

                .done(function (data) {
                    // Show formatted JSON on webpage.
                    $("#responseTextArea").val(JSON.stringify(data.description.captions, null, 2));
                })

                .fail(function (jqXHR, textStatus, errorThrown) {
                    // Display error message.
                    var errorString = (errorThrown === "") ? "Error. " :
                        errorThrown + " (" + jqXHR.status + "): ";
                    errorString += (jqXHR.responseText === "") ? "" :
                        jQuery.parseJSON(jqXHR.responseText).message;
                    alert(errorString);
                });
        };
    </script>

    <h1>Analisa Gambar:</h1>
    Masukkan URL dari Gambar, kemudian klik tombol <strong>Analisa Gambar</strong>.
    <br><br>
    Gambar yang Dianalisa:
    <input type="text" name="inputImage" id="inputImage"
        value=" https://idwprojectstorage.blob.core.windows.net/gambar/robot.jpg" />
    <button onclick="processImage()">Analisa Gambar</button>
    <br><br>
    <div id="wrapper" style="width:1020px; display:table;">
        <div id="jsonOutput" style="width:600px; display:table-cell;">
            Deskripsi Gambar:
            <br><br>
            <textarea id="responseTextArea" class="UIInput" style="width:580px; height:400px;"></textarea>
        </div>
        <div id="imageDiv" style="width:420px; display:table-cell;">
            Sumber Gambar:
            <br><br>
            <img id="sourceImage" width="400" />
        </div>
    </div>

</body>

</html>

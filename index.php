<?php 
session_start();
error_reporting(E_ERROR | E_PARSE);

include_once 'inc/function.find_date.php';
include_once 'inc/docx.php';
include_once 'inc/PdfParser.php';
function reArrayFiles(&$file_post)
{
    $file_ary = [];
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i = 0; $i < $file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }

    return $file_ary;
}

if (isset($_POST) && isset($_POST['Do'])) {
    $target_dir = 'resumes/';
    if (!is_dir($target_dir)) {
        mkdir('resumes');
    }
    $glob = glob('resumes/*.*');
    $glob = sprintf('%02d', count($glob) + 1);
    $files = reArrayFiles($_FILES['offer-main']);
    $details = [];
    foreach ($files as $fileToUpload) {
        $do = 'offer-main-form';

        $uploadOk = 1;
        $imageFileType = pathinfo($fileToUpload['name'], PATHINFO_EXTENSION);
        $target_file = $target_dir.$glob.'-'.$fileToUpload['name'];

        $check = getimagesize($fileToUpload['tmp_name']);
        if ($fileToUpload['size'] > 500000) {
            $msg = 'Sorry, your file is too large.';
            $uploadOk = 0;
        }
        // Allow certain file formats
        if ($imageFileType != 'docx' && $imageFileType != 'doc' && $imageFileType != 'xlsx' && $imageFileType != 'pptx' && $imageFileType != 'pdf') {
            $msg = 'Sorry, invalid file type.';
            $uploadOk = 0;
        }
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            // $msg = "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($fileToUpload['tmp_name'], $target_file)) {
                $msg = 'The Resume has been uploaded.';

                if ($imageFileType == 'pdf') {
                    $pdfObj = new PdfParser();
                    $resumeText = $pdfObj->parseFile($target_file);
                    
                } else {
                    $docObj = new DocxConversion($target_file);
                    $resumeText = $docObj->convertToText();
                }

                $fileInfo = explode(PHP_EOL, $resumeText);
                $records = [];
                foreach ($fileInfo as $row) {
                    $parts = preg_split('/(?<=[.?!])\s+(?=[a-z])/i', $row);
                    foreach ($parts as $part) {
                        if ($part == '') {
                            continue;
                        }

                        //  ***************  EMAIL  **************

                        if (strpos($part, '@') || strpos($part, 'mail')) {
                            $pattern = '/[a-z0-9_\-\+]+@[a-z0-9\-]+\.([a-z]{2,3})(?:\.[a-z]{2})?/i';
                            preg_match_all($pattern, $part, $matches);
                            foreach ($matches[0] as $match) {
                                $records['email'][] = $match;
                            }
                        }


                        //  ***************  MOBILE  **************

                        preg_match_all('/\d{10}/', $part, $matches);
                        if (count($matches[0])) {
                            foreach ($matches[0] as $mob) {
                                $records['mobile'][] = $mob;
                            }
                        }

                        preg_match_all('/\d{12}/', $part, $matches);
                        if (count($matches[0])) {
                            foreach ($matches[0] as $mob) {
                                $records['mobile'][] = $mob;
                            }
                        }

                        preg_match_all('/(\d{5}) (\d{5})/', $part, $matches);
                        if (count($matches[0])) {
                            foreach ($matches[0] as $mob) {
                                $records['mobile'][] = $mob;
                            }
                        }



                        //  ***************  NAME  **fe************

                        

                        if (isset($records['email'])) {
                            foreach ($records['email'] as $email) {
                                $e = explode('@', $email);
                                $records['name'][] = $e[0];

                            }
                        }
                    }
                }

                foreach ($records as $key => $value) {
                    $records[$key] = array_unique($value);
                }
                $records['text'] = $resumeText;
                $records['filename'] = $target_file;

                $details[] = $records;


            } else {
                $msg = 'Sorry, there was an error uploading your file.';
                break;
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

 <?php include_once 'inc/head.php'; ?>

</head>

<body>

    <div id="wrapper">
        
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h2 class="page-header">Resume Parser</h2>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-md-12 ">

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Upload your resume </h3>
                    </div>
                    <div class="panel-body">
                        
                        <div class="tab-content">
                            <div class="tab-pane fade in active" id="mainOffer">
                                <h3></h3>
                                <?php if (!isset($resumeText)) { ?>
                                <div class="col-md-6 col-sm-offset-3">
                                    <form id="offer-main-form" enctype="multipart/form-data" method="post">
                                       
                                       
                                        <div class="form-group text-center">
                                            <input type="file" class="hidden" name="offer-main[]" multiple id="offer-main">
                                            <input type="hidden" name="Do" value="ChangeOfferMain">
                                            <input type="hidden" name="table" value="offer">
                                            <div class="btn btn-primary" onclick="$('#offer-main').click()" id="btn-offer-main">Upload Resume</div>&nbsp;&nbsp;&nbsp;
                                            <span id="info-offer-main"></span>
                                        </div>
                                    </form>
                                </div>
                                <?php 
                            } else {?>
                            	<div class="col-sm-6 col-sm-offset-3">
	                                <?php foreach ($details as $key => $records) { ?>
	                                <h1></h1>
                                    <h3>
                                        <?php $fname = explode('/', $records['filename']);echo end($fname) ?>
                                    </h3>
                                    <form action="" id="Records<?php echo $key ?>" name="Records<?php echo $key ?>" method="post">
                                       
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <select id="name" class="form-control">
                                            <?php 
                                                if (isset($records['name'])) {
                                                    foreach ($records['name'] as $value) {                  
                                                        ?><option value="<?php echo $value ?>"><?php echo $value ?></option><?php
                                                    }
                                                }?>
                                            </select>
                                           
                                        </div>

                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <select id="email" class="form-control">
                                            <?php 
                                                if (isset($records['email'])) {
                                                    foreach ($records['email'] as $value) {
                                                        ?><option value="<?php echo $value ?>"><?php echo $value ?></option><?php

                                                    }
                                                }?>                                           
                                            </select>
                                            
                                        </div>

                                        <div class="form-group">
                                            <label for="mobile">Mobile No</label>
                                            <select id="mobile" class="form-control">
                                            <?php 
                                                if (isset($records['mobile'])) {
                                                    foreach ($records['mobile'] as $value) {
                                                        ?><option value="<?php echo $value ?>"><?php echo $value ?></option><?php

                                                    }
                                                }?>                                        
                                            </select>
                                            
                                        </div>                                     
                                    </form>
                                    <h1></h1>
	                                <?php } ?>
                            	</div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

                </div>
            <!-- /.row -->
            </div>
        </div>
        <!-- /#page-wrapper -->
        
    </div>
    <!-- /#wrapper -->
    <?php include_once 'inc/foot.php'; ?>
    <script>    
        $(function () {
        	$('#offer-main').on('change',function () {
        		$('#offer-main-form').submit()
        	})
        	$('#resumeList').dataTable()
            <?php if (isset($msg)) {
    ?>
                $('#<?php echo $do ?> .alert').show(300).delay(3000).hide(200).find('p').text("<?php echo $msg ?>")
            <?php 
} ?>
        })
    </script>
</body>

</html>

<?php
/*
 ******************************************************************************************************************
 *  Author:           Nam Tran, Grey Hat Apps
 *  Email Address:    nam@greyhatapps.com
 *  Date Created:     2/23/2011
 *
 ******************************************************************************************************************
 *  Classes: AmazonS3, AmazonS3Account
 *
 *  Classes that interact with the Amazon S3 service, allowing you to upload content to S3.
 *
 ******************************************************************************************************************
 */
  include_once("includes/php/s3.php");

/*
 ******************************************************************************************************************
 *  Class: AmazonS3
 *
 *  Class that interacts with the Amazon S3 service.
 ******************************************************************************************************************
 */
  class AmazonS3
  {
  // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  // +  upload
  // +
  // +  Uploads a file to the specified S3 share on Amazon.
  // +
  // +  $pAmazonS3Account: an AmazonS3Account object containing S3 user credentials
  // +  $pFilePath:        the local path of the file that will be uploaded
  // +  $pBucketName:      the S3 bucket name (ie. 'dealseeq')
  // +  $pBucketPath:      the S3 bucket path where the file will be uploaded to (ie. 'avatars/')
  // +  $pACLPermissions:  permissions the file will have on the S3 server (ie. )
  // +
  // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    static public function upload(&$pMsgObj, $pAmazonS3Account, $pFilePath, $pBucketName, $pBucketPath, $pACLPermissions=S3::ACL_PUBLIC_READ)
    {
      // check if file path exists
      if(!file_exists($pFilePath) || !is_file($pFilePath))
      {
        $pMsgObj->errorCode = -1;
        $pMsgObj->msg = "The file or filepath does not exist.";
        return false;
      }

      // check to see if the CURL extension is loaded
      if(!extension_loaded('curl') && !@dl(PHP_SHLIB_SUFFIX == 'so' ? 'curl.so' : 'php_curl.dll'))
      {
        $pMsgObj->errorCode = -1;
        $pMsgObj->msg = "The CURL extension has not been loaded.";
        return false;
      }

      // form full s3 path using s3 bucket name and local file path
      $array_localpath = pathinfo($pFilePath);
      $uploadFileName = str_replace("." . $array_localpath["extension"], "", $array_localpath["basename"]) . "." . $array_localpath["extension"];
      $bucketFilePath = $pBucketPath . $uploadFileName;

      $s3 = new S3($pAmazonS3Account->accessKey, $pAmazonS3Account->secretKey);
      if($s3->putObjectFile($pFilePath, $pBucketName, $bucketFilePath, $pACLPermissions))
      {
        $pMsgObj->errorCode = 1;
        $pMsgObj->msg = "'$pFilePath' has been successfully uploaded to the following S3 location: '$bucketFilePath'";
        return true;
      }

      $pMsgObj->errorCode = -1;
      $pMsgObj->msg = "Unable to upload '$pFilePath' to the following S3 location: '$bucketFilePath'";
      return false;
    }
  }

/*
 ******************************************************************************************************************
 *  Class: AmazonS3
 *
 *  Class that represents an Amazon S3 account
 ******************************************************************************************************************
 */
  class AmazonS3Account
  {
    public $accessKey;
    public $secretKey;

  // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  // +  constructor
  // +
  // +  Class constructor
  // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    function __construct($pAccessKey, $pSecretKey)
    {
      $this->accessKey = $pAccessKey;
      $this->secretKey = $pSecretKey;
    }
  }
?>

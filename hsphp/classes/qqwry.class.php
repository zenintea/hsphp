<?php 

 //这些是核心部分,可以写到一个单独的php里,用的时候直接include就可以  
!defined('ZF') && exit('Access Denied');
 class qqwry{

     private $StartIP=0;  

	 private $EndIP=0;  

	 public $Country='';  

     private $Local='';  

     private $CountryFlag=0;  

	 private $fp;  

     private $FirstStartIp=0;  

	 private $LastStartIp=0;  

     private $EndIpOff=0 ;  

     function getStartIp($RecNo){  

      $offset=$this->FirstStartIp+$RecNo * 7 ;  

      @fseek($this->fp,$offset,SEEK_SET) ;  

      $buf=fread($this->fp ,7) ;  

      $this->EndIpOff=ord($buf[4]) + (ord($buf[5])*256) + (ord($buf[6])* 256*256);  

      $this->StartIp=ord($buf[0]) + (ord($buf[1])*256) + (ord($buf[2])*256*256) + (ord($buf[3])*256*256*256);  

      return $this->StartIp;  

     }  

     function getEndIp(){  

      @fseek ( $this->fp , $this->EndIpOff , SEEK_SET ) ;  

      $buf=fread ( $this->fp , 5 ) ;  

      $this->EndIp=ord($buf[0]) + (ord($buf[1])*256) + (ord($buf[2])*256*256) + (ord($buf[3])*256*256*256);  

      $this->CountryFlag=ord ( $buf[4] ) ;  

      return $this->EndIp ;  

     }  

     function getCountry(){  

      switch ( $this->CountryFlag ) {  

         case 1:  

         case 2:  

          $this->Country=$this->getFlagStr ( $this->EndIpOff+4) ;  

          //echo sprintf('EndIpOffset=(%x)',$this->EndIpOff );  

          $this->Local=( 1 == $this->CountryFlag )? '' : $this->getFlagStr ( $this->EndIpOff+8);  

          break ;  

         default :  

          $this->Country=$this->getFlagStr ($this->EndIpOff+4) ;  

          $this->Local=$this->getFlagStr ( ftell ( $this->fp )) ;  

      }  

     }  

     function getFlagStr ($offset){  

      $flag=0 ;  

      while(1){  

         @fseek($this->fp ,$offset,SEEK_SET) ;  

         $flag=ord(fgetc($this->fp ) ) ;  

         if ( $flag == 1 || $flag == 2 ) {  

          $buf=fread ($this->fp , 3 ) ;  

          if ($flag==2){  

             $this->CountryFlag=2;  

             $this->EndIpOff=$offset - 4 ;  

          }  

          $offset=ord($buf[0]) + (ord($buf[1])*256) + (ord($buf[2])* 256*256);  

         }  

         else{  

          break ;  

         }  

      }  

      if($offset<12)  

         return '';  

      @fseek($this->fp , $offset , SEEK_SET ) ;  

    

      return $this->getStr();  

     }  

     function getStr ( )  

     {  

      $str='' ;  

      while ( 1 ) {  

         $c=fgetc ( $this->fp ) ;  

         if(ord($c[0])== 0 )  

          break ;  

         $str.= $c ;  

      }  

      return $str ;  

     }  

     function qqwry ($dotip='') {  

         if( !is_string($dotip) || $dotip==''){$this->Country="&#26080;&#27861;&#35782;&#21035;";return;}  

                 if(preg_match("/^127/",$dotip)){$this->Country="&#26412;&#22320;&#32593;&#32476;";return ;}  

         elseif(preg_match("/^192/",$dotip)) {$this->Country="&#23616;&#22495;&#32593;&#20869;";return ;}

    

      $nRet;  

      $ip=$this->IpToInt ( $dotip );  

      $this->fp= fopen(COMM_OT.'qqwry.dat', "rb");  

      if ($this->fp == NULL) {  

          $szLocal= "OpenFileError";  

         return 1;  

      }  

      @fseek ( $this->fp , 0 , SEEK_SET ) ;  

      $buf=fread ( $this->fp , 8 ) ;  

      $this->FirstStartIp=ord($buf[0]) + (ord($buf[1])*256) + (ord($buf[2])*256*256) + (ord($buf[3])*256*256*256);  

      $this->LastStartIp=ord($buf[4]) + (ord($buf[5])*256) + (ord($buf[6])*256*256) + (ord($buf[7])*256*256*256);  

      $RecordCount= floor( ( $this->LastStartIp - $this->FirstStartIp ) / 7);  

      if ($RecordCount <= 1){  

         $this->Country="FileDataError";  

         fclose($this->fp) ;  

         return 2 ;  

      }  

      $RangB= 0;  

      $RangE= $RecordCount;  

   while ($RangB < $RangE-1)  

      {  

      $RecNo= floor(($RangB + $RangE) / 2);  

      $this->getStartIp ( $RecNo ) ;  

    

         if ( $ip == $this->StartIp )  

         {  

          $RangB=$RecNo ;  

          break ;  

         }  

    if ($ip>$this->StartIp)  

         $RangB= $RecNo;  

      else 

         $RangE= $RecNo;  

      }  

      $this->getStartIp ( $RangB ) ;  

      $this->getEndIp ( ) ;  

    

      if ( ( $this->StartIp <= $ip ) && ( $this->EndIp >= $ip ) ){  

         $nRet=0 ;  

         $this->getCountry ( ) ;  

         $this->Local=str_replace("（我们一定要解放台湾！！！）", "", $this->Local);  

      }  

      else{  

         $nRet=3 ;  

         $this->Country='&#26410;&#30693;' ;  

         $this->Local='' ;  

      }  

      fclose ( $this->fp );  

         return $nRet ;  

     }  

     function IpToInt($Ip) {  

      $array=explode('.',$Ip);  

      $Int=($array[0] * 256*256*256) + ($array[1]*256*256) + ($array[2]*256) + $array[3];  

      return $Int;  

     }  

 }   
?>
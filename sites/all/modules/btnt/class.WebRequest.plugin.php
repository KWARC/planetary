<?php
// Copyright (C) 2010-2011, Planetary System Developer Group. All rights reserved.

// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Affero General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU Affero General Public License for more details.

// You should have received a copy of the GNU Affero General Public License
// along with this program. If not, see <http://www.gnu.org/licenses/>

class WebRequest {
	
	/**
	 * Method for doing a GET request.
	 * 
	 * @param string $Host the hostname where the GET request is to be done
	 * @param string $User the username to use for the secure GET
	 * @param string $Pass the password to use for the secure GET
	 * @return the trimmed response received from the GET
	 */
	static public function cURL_GET($Host, $User = false, $Pass = false) {
		$User = ($User == false ? C("TNTBase.Username") : $User);
		$Pass = ($Pass == false ? C("TNTBase.Password") : $Pass);
		
		$Session = curl_init();
		curl_setopt($Session, CURLOPT_URL, $Host);
		curl_setopt($Session, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($Session, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($Session, CURLOPT_USERPWD, "$User:$Pass");
		curl_setopt($Session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		$Output = curl_exec($Session);
		curl_close($Session);
		return trim($Output);	
	}
	
	/**
	 * Method for doing a POST request.
	 * 
	 * @param string $Data the data to be sent via POST
	 * @param string $Host the hostname where the POST request is to be done
	 * @param string $User the username to use for the secure POST
	 * @param string $Pass the password to use for the secure POST
	 * @return the response received by the POST
	 */
	static public function cURL_POST($Data, $Host, $User = false, $Pass = false) {
		$User = ($User == false ? C("TNTBase.Username") : $User);
		$Pass = ($Pass == false ? C("TNTBase.Password") : $Pass);
		
		$Session = curl_init($Host);
		curl_setopt($Session, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($Session, CURLOPT_USERPWD, "$User:$Pass");
		curl_setopt($Session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($Session, CURLOPT_POST, true);
		curl_setopt($Session, CURLOPT_POSTFIELDS, $Data);
		curl_setopt($Session, CURLOPT_HEADER, false);
		curl_setopt($Session, CURLOPT_RETURNTRANSFER, true);
		$Response = curl_exec($Session);
		curl_close($Session);
		return $Response;
	}
		
	/**
	 * Method for doing a PUT request.
	 * 
	 * @param string $Data the data to be sent via PUT
	 * @param string $Host the hostname where the PUT request is to be done
	 * @param string $User the username to use for the secure PUT
	 * @param string $Pass the password to use for the secure PUT
	 * @return the response received by the PUT
	 */
	static public function cURL_PUT($Data, $Host, $Header = array('Content-Type: text/xml'), $User = false, $Pass = false) {
		$User = ($User == false ? C("TNTBase.Username") : $User);
		$Pass = ($Pass == false ? C("TNTBase.Password") : $Pass);
		
		// Start curl
		$Session = curl_init($Host);
		curl_setopt($Session, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($Session, CURLOPT_USERPWD, "$User:$Pass");
		curl_setopt($Session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		
		// Clean up string
		$PutString = stripslashes($Data);
		// Put string into a temporary file
		$PutData = tmpfile();
		// Write the string to the temporary file
		fwrite($PutData, $PutString);
		// Move back to the beginning of the file
		fseek($PutData, 0);
		
		curl_setopt($Session, CURLOPT_HTTPHEADER,  $Header);
		curl_setopt($Session, CURLOPT_RETURNTRANSFER, true);
		// Using a PUT method i.e. -XPUT
		curl_setopt($Session, CURLOPT_PUT, true);
		// Instead of POST fields use these settings
		curl_setopt($Session, CURLOPT_INFILE, $PutData);
		curl_setopt($Session, CURLOPT_INFILESIZE, strlen($PutString));
		
		$Response = curl_exec($Session);
		
		// Close the file
		fclose($PutData);
		// Stop curl
		curl_close($Session);
		
		return $Response;
	}
	
	/**
	 * Method used to send a set of files to using the following format for each part
	 * 
	 * <p>
	 * --__mime-boundary__
	 * Content-Type: __type__
	 * path: __path__
	 * 
	 * __content__
	 * </p>
	 * 
	 * @param string $URL the URL to send the files to
	 * @param array $Files an array of file structs with <b>Content</b>, <b>Path</b> and <b>Content-Type</b> fields
	 * @return the response from the server from the PUT request
	 */
	static public function cURL_PUT_MultipartFiles($Files, $URL) {
		$EOL = "\n";		
		$Data = ''; 
		$Boundary = md5(time());
		// header
		
		$Data .= ''.$EOL; 
		
		foreach($Files as $File) {
			$Data .= '--' . $Boundary . $EOL;
			$Data .= 'Content-Type: '.$File['Content-Type'].$EOL.'path: '.$File['Path']. $EOL . $EOL;
			$Data .= $File['Content'] . $EOL;	
		}
		
		$Data .= '--'.$Boundary.'--'.$EOL;
		$Header = 'Content-Type: multipart/mixed; boundary="' . $Boundary.'"';
		
		$Response = WebRequest::cURL_PUT($Data, $URL, array($Header));
		return $Response;
	}
	
	/**
	 * Method for doing a DELETE request.
	 * 
	 * @param string $Host the hostname where the DELETE request is to be done
	 * @param string $User the username to use for the secure DELETE
	 * @param string $Pass the password to use for the secure DELETE
	 * @return the response received by the POST
	 */
	static public function cURL_DELETE($Host, $User = false, $Pass = false) {
		$User = ($User == false ? C("TNTBase.Username") : $User);
		$Pass = ($Pass == false ? C("TNTBase.Password") : $Pass);
		
		$Session = curl_init($Host);
		curl_setopt($Session, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($Session, CURLOPT_USERPWD, "$User:$Pass");
		curl_setopt($Session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($Session, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($Session, CURLOPT_HEADER, false);
		curl_setopt($Session, CURLOPT_RETURNTRANSFER, true);
		$Response = curl_exec($Session);
		curl_close($Session);
		return $Response;
	}
	
	public function Setup() {
		
	}
}
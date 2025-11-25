<?php

namespace App\Library {

    use App\Models\Apiresponse;
    use Str;
    use Helpers;
    use Illuminate\Support\Facades\Log;

    class PunjikendraLibrary
    {
        protected $api_id;
        protected $base_url;
        protected $api_key;
        protected $secret_key;

        public function __construct()
        {
            $this->api_id      = 10;
            $this->base_url    = 'https://partner.punjikendra.in/';
            $this->api_key     = 'cvm68Jmci32iqAvddL10FAYYuMUbc2N2eG6n3qDYsVA=';
            $this->secret_key  = '212fd98b8dddc286b0a922f4834be6a4820ff0b009541b000fdfc71f0eb9b1eb';

            Log::info("[PunjikendraLibrary] Initialized with API ID {$this->api_id}");
        }

        function transferNow($user_id, $mobile_number, $amount, $beneficiary_name, $account_number, $ifsc_code, $insert_id)
        {
            Log::info("[PunjikendraLibrary][transferNow] Start transfer process", compact('user_id', 'amount', 'beneficiary_name', 'account_number', 'ifsc_code', 'insert_id'));
        
            $url = rtrim($this->base_url, '/') . '/api/transfer_fund';
            $bank_name = $this->getBanknameByIfsc($ifsc_code);
        
            Log::info("[PunjikendraLibrary][transferNow] Bank name fetched", ['bank_name' => $bank_name]);
        
            // JWT payload (ONLY transaction data)
            $payload = [
                "beneName"          => $beneficiary_name,
                "beneAccountNo"     => (int)$account_number,  // API expects integer
                "beneifsc"          => $ifsc_code,
                "beneBankName"      => $bank_name,
                "clientReferenceNo" => (string)$insert_id,
                "amount"            => number_format((float)$amount, 2, '.', '')
            ];
        
            Log::info("[PunjikendraLibrary][transferNow] JWT Payload prepared", $payload);
        
            // Encode JWT
            $token = $this->jwt_encode($payload, $this->secret_key);
        
            $headers = [
                "Authorization: {$token}",
                "Content-Type: application/json",
                "Accept: application/json"
            ];
        
            // Body should ONLY contain Apikey
            $body = json_encode([
                "Apikey" => $this->api_key
            ]);
        
            Log::info("[PunjikendraLibrary][transferNow] Final API Body prepared", ['body' => $body]);
        
            // Execute request
            $response = $this->punjikendraCurlRequest($url, $headers, $body);
        
            Log::info("[PunjikendraLibrary][transferNow] API Response received", ['response' => $response]);
        
            Apiresponse::insertGetId([
                'message'          => $response,
                'api_type'         => $this->api_id,
                'report_id'        => $insert_id,
                'request_message'  => $url . ' ' . $body . ' Headers: ' . json_encode($headers)
            ]);
        
            $res = json_decode($response);
        
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error("[PunjikendraLibrary][transferNow] Invalid JSON response", ['response' => $response]);
                return ['status_id' => 2, 'txnid' => 'Invalid response from API', 'payid' => ''];
            }
        
            $status  = $res->status ?? false;
            $tstatus = $res->tstatus ?? 'INITIATED';
            $message = $res->message ?? '';
        
            Log::info("[PunjikendraLibrary][transferNow] Status values", compact('status', 'tstatus', 'message'));
        
            if (!$status) {
                return ['status_id' => 2, 'txnid' => 'Transaction Failed: ' . $message, 'payid' => ''];
            }
        
            if ($tstatus === 'SUCCESS') {
                return ['status_id' => 1, 'txnid' => $res->bankReferenceNumber ?? '', 'payid' => $res->transactionId ?? ''];
            } elseif ($tstatus === 'Failed') {
                $message = $res->message ?? 'Transaction Failed';
                if (Str::startsWith($message, 'Insufficient balance')) {
                    $message = 'Transaction Failed please try after some time';
                }
                return ['status_id' => 2, 'txnid' => $message, 'payid' => $res->transactionId ?? ''];
            } else {
                return ['status_id' => 3, 'txnid' => '', 'payid' => $res->transactionId ?? ''];
            }
        }

        private function getBanknameByIfsc($ifsc_code)
        {
            Log::info("[PunjikendraLibrary][getBanknameByIfsc] Fetching bank name for IFSC", ['ifsc_code' => $ifsc_code]);

            $url = "https://ifsc.razorpay.com/$ifsc_code";
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_TIMEOUT, 10);
            $data = curl_exec($curl);
            curl_close($curl);

            $res = json_decode($data);

            Log::info("[PunjikendraLibrary][getBanknameByIfsc] IFSC API response", ['response' => $res]);

            return $res->BANK ?? '';
        }

        private function base64url_encode($data)
        {
            return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
        }

        private function jwt_encode($payload, $secret, $alg = 'HS256')
        {
            Log::info("[PunjikendraLibrary][jwt_encode] Encoding JWT");

            $header = ['typ' => 'JWT', 'alg' => $alg];
            $header_encoded  = $this->base64url_encode(json_encode($header));
            $payload_encoded = $this->base64url_encode(json_encode($payload));

            $signature = hash_hmac('sha256', "$header_encoded.$payload_encoded", $secret, true);
            $signature_encoded = $this->base64url_encode($signature);

            return "$header_encoded.$payload_encoded.$signature_encoded";
        }
        
        /**
         * Custom CURL function for Punjikendra API requests with verbose logging
         */
        private function punjikendraCurlRequest($url, $headers, $body)
        {
            $ch = curl_init();
            
            // Create temporary file for verbose output
            $verbose = fopen('php://temp', 'w+');
            
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_VERBOSE => true,
                CURLOPT_STDERR => $verbose,
            ]);
            
            $response = curl_exec($ch);
            $err = curl_error($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            // Get verbose output
            rewind($verbose);
            $verbose_log = stream_get_contents($verbose);
            fclose($verbose);
            
            // Log detailed request information
            Log::info("[PunjikendraLibrary][punjikendraCurlRequest] CURL Details", [
                'http_code' => $http_code,
                'verbose_log' => $verbose_log,
                'request_headers' => $headers,
                'request_body' => $body,
                'response' => $response
            ]);
            
            if ($err) {
                Log::error("[PunjikendraLibrary][punjikendraCurlRequest] CURL Error", ['error' => $err]);
                return json_encode(['status' => false, 'message' => 'CURL Error: ' . $err]);
            }
            
            curl_close($ch);
            
            return $response;
        }
    }
}
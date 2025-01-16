<?php
/**
 * YouTube API Class
 *
 * @since       2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SWYI_YouTube_API' ) ) {

	class SWYI_YouTube_API extends Streamweasels_Youtube_Admin {

		private $token_url = 'https://www.googleapis.com/youtube/v3/channels?part=contentDetails&forHandle=streamweasels';
		private $channel_url = 'https://www.googleapis.com/youtube/v3/channels?part=contentDetails';
		private $playlist_url = 'https://www.googleapis.com/youtube/v3/playlists?part=snippet';
		private $playlist_items = 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet';
		private $auth_token;
		private $game;
		private $team;
		private $channel;
		private $client_id;
		private $client_secret;
		private $token;
		private $debug = false;

		public function __construct() {
			$options = get_option('swyi_options');
			$this->auth_token = (!empty($options['swyi_api_key'])) ? $options['swyi_api_key'] : '';
		}

		public function enable_debug_mode() {
			$this->debug = true;
		}		

		public function check_channels($apiKey = "", $channelIDs = "") {
			$headers = [
				'referer' => isset($_SERVER['HTTP_REFERER']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_REFERER'])) : '',
			];
		
			$channelIDsArray = explode(',', $channelIDs);
			$validCount = 0;
			$invalidCount = 0;
		
			foreach ($channelIDsArray as $channelID) {
				$response = wp_remote_get($this->channel_url . '&key=' . $apiKey . '&id=' . $channelID, [
					'headers' => $headers,
					'timeout' => 15
				]);
		
				if (is_wp_error($response)) {
					$this->swyi_youtube_debug_field('SWYI - Channel Query Failed - ' . $this->channel_url);
					$invalidCount++;
					continue;
				}
		
				$result = wp_remote_retrieve_body($response);
				$result = json_decode($result, true);
		
				if (isset($result['items']) && !empty($result['items'])) {
					foreach ($result['items'] as $item) {
						if (isset($item['contentDetails']['relatedPlaylists']['uploads'])) {
							$this->swyi_youtube_debug_field('SWYI - Channel Query Success - Playlist ID (' . $item['contentDetails']['relatedPlaylists']['uploads'] . ') found for Channel (' . $channelID . ')');
							$validCount++;
						} else {
							$invalidCount++;
						}
					}
				} else {
					$this->swyi_youtube_debug_field('SWYI - Channel Query Failed - ' . $this->channel_url . '&key=' . $apiKey . '&id=' . $channelID);
					$invalidCount++;
				}
			}
		
			return [
				'valid' => $validCount,
				'invalid' => $invalidCount
			];
		}

        public function check_playlist($apiKey="", $playlistID="") {

			$headers = [
				'referer' => isset($_SERVER['HTTP_REFERER']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_REFERER'])) : '',
			];

			$response = wp_remote_get( $this->playlist_url.'&key='.$apiKey.'&id='.$playlistID, [
				'headers' => $headers,
				'timeout' => 15
			]);

			if ( is_wp_error( $response ) ) {
				$this->swyi_youtube_debug_field('SWYI - Playlist Query Failed - '.$this->playlist_url.'&key='.$apiKey.'&id='.$playlistID);
			}
			$result = wp_remote_retrieve_body( $response );
			$result = json_decode( $result, true );

            if  (isset($result['items']) && isset($result['items'][0]['snippet'])) {
                    $this->swyi_youtube_debug_field('SWYI - Playlist Query Success - Playlist ID ('.$playlistID.') found.');
                    return true;
                } else {
                    $this->swyi_youtube_debug_field('SWYI - Playlist Query Failed - '.$this->playlist_url.'&key='.$apiKey.'&id='.$playlistID);
                    return false;
                }
    	}

		public function check_shorts($apiKey="", $channelID="") {

			$channelIdExploded = explode(',', $channelID);
			if (count($channelIdExploded) > 1) {
				$channel = $channelIdExploded[0];
			} else {
				$channel = $channelID;
			}
			
			if (strpos($channel, "UC") === 0) {
				$channel = "UUSH" . substr($channel, 2);
			} else {
				$this->swyi_youtube_debug_field('SWYI - Playlist Shorts Query Failed - Channel does not start with UC');
				return false;
			}

			$headers = [
				'referer' => isset($_SERVER['HTTP_REFERER']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_REFERER'])) : '',
			];

			$response = wp_remote_get( $this->playlist_items.'&key='.$apiKey.'&playlistId='.$channel.'&maxResults=100', [
				'headers' => $headers,
				'timeout' => 15
			]);

			if ( is_wp_error( $response ) ) {
				$this->swyi_youtube_debug_field('SWYI - Playlist Shorts Query Failed - '.$this->playlist_items.'&key='.$apiKey.'&playlistId='.$channel);
			}
			$result = wp_remote_retrieve_body( $response );
			$result = json_decode( $result, true );

            if  (isset($result['items']) && isset($result['items'][0]['snippet']['resourceId']['videoId'])) {
				$shortsString = '';
				foreach ($result['items'] as $item) {
					if (isset($item['snippet']['resourceId']['videoId'])) {
						$videoId = $item['snippet']['resourceId']['videoId'];
						$shortsString .= $videoId . ',';
					}
				}
				return $shortsString;
			} else {
				$this->swyi_youtube_debug_field('SWYI - Playlist Shorts Query Failed - '.$this->playlist_items.'&key='.$apiKey.'&playlistId='.$channel);
				return false;
			}
		}

		public function check_token($apiKey="") {

			$headers = [
				'referer' => isset($_SERVER['HTTP_REFERER']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_REFERER'])) : '',
			];

			$response = wp_remote_get( $this->token_url.'&key='.$apiKey, [
				'headers' => $headers,
				'timeout' => 15
			]);

			if ( is_wp_error( $response ) ) {
				$this->swyi_youtube_debug_field('SWYI - Token Query Failed - '.$this->token_url.'&key='.$apiKey);
				return array('error');
			}
			$result = wp_remote_retrieve_body( $response );
            $resultCode = wp_remote_retrieve_response_code( $response );

			if ($resultCode == 400 || $resultCode == 403) {
				// Invalid API Key
				$this->swyi_youtube_debug_field('SWYI - Token Query Failed - '.$this->token_url.'&key='.$apiKey);
				$this->swyi_youtube_debug_field($result);
			}

            return $resultCode;
		}

		public function swyi_fetch_videos(WP_REST_Request $request) {

			$headers = [
				'referer' => isset($_SERVER['HTTP_REFERER']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_REFERER'])) : '',
			];			

			$nonce = $request->get_header('X-WP-Nonce');
			if (!wp_verify_nonce($nonce, 'wp_rest')) {
				return new WP_REST_Response('Nonce verification failed', 403);
			}
	
			$authToken = $this->auth_token;
			$baseUrl = "https://www.googleapis.com/youtube/v3/playlistItems?part=snippet,contentDetails,status&order=date&type=video";
			
			$queryParams = [];
			$playlistId = $request->get_param('playlistId');
			if (!empty($playlistId)) {
				$queryParams['playlistId'] = $playlistId;
			}
	
			$pagination = $request->get_param('pageToken');
			if (!empty($pagination)) {
				$queryParams['pageToken'] = $pagination;
			}

			$maxResults = $request->get_param('maxResults');
			if (!empty($maxResults)) {
				$queryParams['maxResults'] = $maxResults;
			}		
			
			if (!empty($authToken)) {
				$queryParams['key'] = $authToken;
			}	

			$queryString = http_build_query($queryParams, '', '&', PHP_QUERY_RFC3986);
			$url = $baseUrl . '&' . $queryString;
	
			$response = wp_remote_get($url, [
				'headers' => $headers,
				'timeout' => 15
			]);
	
			$errorResponse = $this->swyi_handle_twitch_errors($response, $url, 'Fetch Videos');
			if ($errorResponse instanceof WP_REST_Response) {
				return $errorResponse;
			}
	
			$body = wp_remote_retrieve_body($response);
			$data = json_decode($body);
	
			return new WP_REST_Response($data, 200);
		}

		public function swyi_check_live_status(WP_REST_Request $request) {

			$headers = [
				'referer' => isset($_SERVER['HTTP_REFERER']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_REFERER'])) : '',
			];			

			$nonce = $request->get_header('X-WP-Nonce');
			if (!wp_verify_nonce($nonce, 'wp_rest')) {
				return new WP_REST_Response('Nonce verification failed', 403);
			}
	
			$authToken = $this->auth_token;
			$baseUrl = "https://www.googleapis.com/youtube/v3/videos?part=snippet,liveStreamingDetails";
			
			$queryParams = [];
			$id = $request->get_param('id');
			if (!empty($id)) {
				$queryParams['id'] = $id;
			}	
			
			if (!empty($authToken)) {
				$queryParams['key'] = $authToken;
			}	

			$queryString = http_build_query($queryParams, '', '&', PHP_QUERY_RFC3986);
			$url = $baseUrl . '&' . $queryString;
	
			$response = wp_remote_get($url, [
				'headers' => $headers,
				'timeout' => 15
			]);
	
			$errorResponse = $this->swyi_handle_twitch_errors($response, $url, 'Check Live Status');
			if ($errorResponse instanceof WP_REST_Response) {
				return $errorResponse;
			}
	
			$body = wp_remote_retrieve_body($response);
			$data = json_decode($body);
	
			return new WP_REST_Response($data, 200);
		}		

		private function swyi_handle_twitch_errors($response, $url, $context = 'Fetch Streams') {
			// Check for errors in the response
			if (is_wp_error($response)) {
				$this->swyi_youtube_debug_field('WP Error received on the following URL: ' . $url);
				return new WP_REST_Response($response->get_error_message(), 500);
			}
	
			$response_code = wp_remote_retrieve_response_code($response);
			if ($response_code != 200) {
				$this->swyi_youtube_debug_field($context . ' returned status code: ' . $response_code);
				$this->swyi_youtube_debug_field($context . ' request URL: ' . $url);
				$body = wp_remote_retrieve_body($response);
				$data = json_decode($body, true);
				$errorMessage = $data['message'] ?? 'No message received...';
				$this->swyi_youtube_debug_field($context . ' returned error message: ' . $errorMessage);
				return new WP_REST_Response("Error in " . $context . ': ' . $response_code . " - " . $errorMessage, $response_code);
			}

		}

		private function debug_log( $data ) {
			if ( !$this->debug ) {
				return;
			}
			swti_twitch_debug_log( $data );
		}

		private function debug_field( $data ) {
			if ( !$this->debug ) {
				return;
			}			
			swti_twitch_debug_field( $message );
		}
	}
}
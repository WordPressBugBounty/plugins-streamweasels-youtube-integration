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

if ( ! class_exists( 'SWYI_YouTube_API_Cache' ) ) {

    class SWYI_YouTube_API_Cache {

        private $token_url = 'https://www.googleapis.com/youtube/v3/channels?part=contentDetails';
        private $channel_url = 'https://www.googleapis.com/youtube/v3/channels?part=contentDetails';
        private $playlist_url = 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet,contentDetails,status';
        private $game;
        private $team;
        private $channel;
        private $client_id;
        private $client_secret;
        private $token;
        private $debug = false;

    
        public function __construct() {
            $this->options = get_option('swyi_options');
            $this->channel_id = ( isset ( $this->options['swyi_channel_id'] ) ) ? $this->options['swyi_channel_id'] : '';
            $this->playlist_id = ( isset ( $this->options['swyi_playlist_id'] ) ) ? $this->options['swyi_playlist_id'] : '';
            $this->api_key = ( isset ( $this->options['swyi_api_key'] ) ) ? $this->options['swyi_api_key'] : '';
            
        }
    
        public function get_channel_upload_id() {
       
            if (($this->channel_id == '' && $this->playlist_id == '') || $this->api_key == '') {
                swyi_youtube_debug_field('SWYI Cache - get_channel_upload_id failed - Channel ID / Playlist or API Key is empty');
                return false;
            }

            $transient_name = 'swyi_upload_id_'.$this->channel_id;
            $cached_data = get_transient($transient_name);

            if ($this->playlist_id !== '') {
                return $this->playlist_id;
            }            

            // If cached data exists and has not expired, return it
            if ($cached_data !== false) {
                swyi_youtube_debug_field('SWYI Cache - skipping get_channel_upload_id - upload ID found in cache');
                return $cached_data;
            }

			$headers = [
				'referer' => isset($_SERVER['HTTP_REFERER']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_REFERER'])) : '',
			];
			
			$response = wp_remote_get( $this->channel_url.'&key='.$this->api_key.'&id='.$this->channel_id, [
				'headers' => $headers,
				'timeout' => 15
			]);

			if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) != 200 ) {
				swyi_youtube_debug_field('SWYI Cache - get_channel_upload_id Failed - '.$this->channel_url);
                swyi_youtube_debug_field($response['body']);
                return false;
			}  

            $result = wp_remote_retrieve_body( $response );
			$result = json_decode( $result, true );

            if  (isset($result['items']) && isset($result['items'][0]['contentDetails']['relatedPlaylists']['uploads'])) {
                swyi_youtube_debug_field('SWYI Cache - Channel Query Success - Playlist ID ('.$result['items'][0]['contentDetails']['relatedPlaylists']['uploads'].') found for Channel ('.$this->channel_id.')');
                $uploadId = $result['items'][0]['contentDetails']['relatedPlaylists']['uploads'];
                // cache for a year
                set_transient($transient_name, $uploadId, 31536000);
            } else {
                swyi_youtube_debug_field('SWYI Cache - get_channel_upload_id failed - '.$this->channel_url.'&key='.$this->api_key.'&id='.$this->channel_id);
                return false;
            }

            return $uploadId;
        }

        public function get_channel_videos($uploadId, $forceRefresh = false) {

            if (($this->channel_id == '' && $this->playlist_id == '') || $this->api_key == '') {
                swyi_youtube_debug_field('SWYI Cache - get_channel_videos failed - Channel ID / Playlist or API Key is empty');
                return false;
            }

            if ($uploadId == '') {
                swyi_youtube_debug_field('SWYI Cache - get_channel_videos failed - Upload ID is empty');
                return false;
            }

            $transient_name = 'swyi_video_cache';
            $cached_data = get_transient($transient_name);

            // If cached data exists and has not expired, return it
            if ($cached_data !== false && $forceRefresh == false) {
                if ($cached_data['channelID'] == $this->channel_id || $cached_data['channelID'] == $this->playlist_id) {
                    swyi_youtube_debug_field('SWYI Cache - skipping get_channel_videos - videos found in cache');
                    return $cached_data;
                }
            }

            $headers = [
				'referer' => isset($_SERVER['HTTP_REFERER']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_REFERER'])) : '',
			];

			$response = wp_remote_get( $this->playlist_url.'&key='.$this->api_key.'&playlistId='.$uploadId.'&maxResults=50', [
				'headers' => $headers,
				'timeout' => 15
			]);

			if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) != 200 ) {
				swyi_youtube_debug_field('SWYI Cache - get_channel_videos Failed - '.$this->playlist_url.'&key='.$this->api_key.'&id='.$uploadId);
                swyi_youtube_debug_field($response['body']);
                return false;
			}

			$result = wp_remote_retrieve_body( $response );
			$result = json_decode( $result, true );      
            
            if ($this->channel_id) {
                $transientData['channelID'] = $this->channel_id;
            }
            if ($this->playlist_id) {
                $transientData['channelID'] = $this->playlist_id;
            }            
            $transientData['items'] = []; // Initialize the 'items' array

            if (isset($result['items']) && !empty($result['items'])) {
                foreach ($result['items'] as $videoData) {
                    $snippet = $videoData['snippet'];
                    $channelTitle = $snippet['channelTitle'];
                    $title = $snippet['title'];
                    $thumbnailUrl = $snippet['thumbnails']['medium']['url'];
                    $publishedAt = $snippet['publishedAt'];
                    $videoID = $snippet['resourceId']['videoId'];
                    $privacyStatus = $videoData['status']['privacyStatus'];

                    $snippetArray = [
                        'thumbnails' => ['medium' => ['url' => $thumbnailUrl]],
                        'title' => $title,
                        'publishedAt' => $publishedAt,
                        'resourceId' => ['videoId' => $videoID],
                        'privacyStatus' => $privacyStatus,
                        'channelTitle' => $channelTitle,
                    ];
                
                    $videoItem = [
                        'snippet' => $snippetArray,
                        'status' => ['privacyStatus' => $privacyStatus],
                    ];
                
                    $transientData['items'][] = $videoItem;
                }
            }

            if (!empty($transientData) && !empty($transientData['items'])) {
                // Cache the data for 24 hours (86400 seconds)
                swyi_youtube_debug_field('SWYI Cache - Cache generation success');
                set_transient($transient_name, $transientData, 86400);
            }

            return $transientData;            
        }
    
    }
    
}
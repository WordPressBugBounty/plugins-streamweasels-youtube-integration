function streamWeaselsYouTube(opts) {
    this._opts = opts;
	this.uuid = opts.uuid;
    this.channelID = opts.channelID;
    this.channelTitle = '';
    this.playlistID = opts.playlistID;
    this.liveStreamID = opts.liveStreamID;
    this.pageToken = opts.pageToken;
    this.limit = opts.limit;
    this.layout = opts.layout;
    this.slideCount = opts.slideCount;
    this.titleOverride = opts.titleOverride;
    this.pagination = opts.pagination;
    this.embed = opts.embed;
    this.embedMuted = opts.embedMuted;
    this.showOffline = opts.showOffline;
    this.showOfflineText = opts.showOfflineText;
    this.showOfflineImage = opts.showOfflineImage;    
    this.autoload = opts.autoload;
    this.autoplay = opts.autoplay;
    this.logoImage = opts.logoImage,
    this.logoBgColour = opts.logoBgColour,
    this.logoBorderColour = opts.logoBorderColour,
    this.tileSorting = opts.tileSorting,
    this.tileBgColour = opts.tileBgColour,
    this.tileTitleColour = opts.tileTitleColour,
    this.tileSubtitleColour = opts.tileSubtitleColour,
    this.hoverColour = opts.hoverColour,
    this.enableCache = opts.enableCache,
    this.hideShorts = opts.hideShorts,
    this.shortsIds = opts.shortsIds,
    this.translationsLive = opts.translationsLive,
    this.translationsViews = opts.translationsViews,
    this.translationsNextPage = opts.translationsNextPage,
    this.translationsPrevPage = opts.translationsPrevPage,
    this.nonce = opts.nonce;
    this.wrapper = document.querySelector('.cp-streamweasels-youtube--'+this.uuid);
    this.target = this.wrapper.querySelector('.cp-streamweasels-youtube__streams');
    this.loading = this.wrapper.querySelector('.cp-streamweasels-youtube__loader');
    this.player = this.wrapper.querySelector('.cp-streamweasels-youtube__player');
    if (this.liveStreamID) {
        // quota cost: 2x+1 where x is number of channels
        this._getUploadID(this.liveStreamID);
    } else if (this.playlistID) {
        // quota cost: 2
        if (this.enableCache && streamWeaselsYouTubeVars.cacheData.channelID == this.playlistID) {
            console.log('pulling playlist from YouTube cache');
            this._appendVideos(streamWeaselsYouTubeVars.cacheData.items, 0);
        } else {
            this._getChannelVideos([this.playlistID], this.pageToken);
        }
    } else if (this.channelID) {
        if (this.layout=="showcase") {
            this._getShortsID(this.channelID);
        } else {
            // quota cost: 3
            if (this.enableCache && streamWeaselsYouTubeVars.cacheData.channelID == this.channelID) {
                console.log('pulling channel from YouTube cache');
                this._appendVideos(streamWeaselsYouTubeVars.cacheData.items, 0);
            } else {
                this._getUploadID(this.channelID);
            }
        }
    } else {
        this._getUploadID('UCAuUUnT6oDeKwE6v1NGQxug');
    }
}

streamWeaselsYouTube.prototype = Object.create(null,{
    constructor: {
        value: streamWeaselsYouTube
    },
    _handleApiResponse: {
        value: function(data, functionName) {
            if (data.code === "rest_cookie_invalid_nonce") {
                console.error(`${functionName} - nonce validation failed:`, data);
                return false;
            }
            
            if (!Array.isArray(data.items)) {
                console.error(`${functionName} - unexpected data format:`, data);
                return false;
            }
            
            return true;
        }
    },    
    _getUploadID:{
        value: function(channelID){

            var idsArray = channelID.split(',');
            var modifiedIDs = [];

            for (var i = 0; i < idsArray.length; i++) {
                var id = idsArray[i].trim();
                if (id.indexOf("UC") === 0) {
                    modifiedIDs.push("UU" + id.substring(2));
                } else {
                    console.warn('ID does not start with "UC":', id);
                    modifiedIDs.push(id);
                }
            }       
            
            if (this.liveStreamID) {
                this.wrapper.dataset.total = this.liveStreamID.split(',').length;
                this._getLiveVideos(modifiedIDs)
            } else if (this.channelID) {
                this._getChannelVideos(modifiedIDs,false)
            }
        }
    },
    _getShortsID:{
        value: function(channelID){

            var idsArray = channelID.split(',');
            var modifiedIDs = [];

            for (var i = 0; i < idsArray.length; i++) {
                var id = idsArray[i].trim();
                if (id.indexOf("UC") === 0) {
                    modifiedIDs.push("UUSH" + id.substring(2));
                } else {
                    console.warn('ID does not start with "UC":', id);
                    modifiedIDs.push(id);
                }
            }              
            this._getShorts(modifiedIDs);
        }
    },      	
    _getChannelVideos: {
        value: function(UploadsID, pageToken = false) {
    
            var args = '&maxResults=' + this.limit;
            var xhr = [];
            var videosArray = [];
            var requestCount = 0;
    
            for (var i = 0; i < UploadsID.length; i++) {
                (function(i) {
                    var query = '&playlistId=' + UploadsID[i];
                    if (pageToken) {
                        query += '&pageToken=' + pageToken;
                    }
    
                    xhr[i] = new XMLHttpRequest();
                    xhr[i].open("GET", "/?rest_route=/streamweasels-youtube/v1/fetch-videos/"+query+args);
                    xhr[i].setRequestHeader("X-WP-Nonce", this.nonce);
                    console.log("_getChannelVideos", "https://www.googleapis.com/youtube/v3/playlistItems?part=snippet,contentDetails"+query+args);
                    xhr[i].onreadystatechange = function() {
                        if (xhr[i].readyState === 4) {
                            requestCount++;
                            if (xhr[i].status === 200) {
                                try {
                                    var data = JSON.parse(xhr[i].responseText);

                                    if (!this._handleApiResponse(data, '_getChannelVideos')) {
                                        return;
                                    }

                                    // Logic to handle the parsed data
                                    if (UploadsID.length == 1) {
                                        if (data.items && data.items.length > 0) {
                                            this._appendVideos(data.items);
                                        }
                                        // Handle pagination
                                        if (this.pagination && (this.layout !== 'feature') && (this.layout !== 'status') && (!this.liveStreamID) && (data.nextPageToken || data.prevPageToken)) {
                                            this._updatePagination(data.nextPageToken, data.prevPageToken, UploadsID);
                                        }
                                    } else {
                                        videosArray = videosArray.concat(data.items || []);
                                        if (requestCount === UploadsID.length) {
                                            // Sort and append videos as before...
                                            this._appendVideos(videosArray.sort(function(a, b) {
                                                var dateA = new Date(a.snippet.publishedAt), dateB = new Date(b.snippet.publishedAt);
                                                return dateB - dateA;
                                            }));
                                        }
                                    }
                                } catch (e) {
                                    console.error('Failed to parse response for request index:', i, e);
                                }
                            } else {
                                console.error('HTTP Error for request index:', i, 'Status:', xhr[i].status);
                            }
                        }
                    }.bind(this);
    
                    xhr[i].send();
                }.bind(this))(i);
            }
        }
    },
    _getLiveVideos: {
        value: function(UploadsID) {
            var args = '&maxResults=50';
    
            for (var i = 0; i < UploadsID.length; i++) {
                (function(i) {
                    var xhr = new XMLHttpRequest();
                    var playlistId = UploadsID[i];
                    var query = '&playlistId=' + playlistId;
                    xhr.open("GET", "/?rest_route=/streamweasels-youtube/v1/fetch-videos/"+query+args);
                    xhr.setRequestHeader("X-WP-Nonce", this.nonce);
                    console.log("_getLiveVideos", "https://www.googleapis.com/youtube/v3/playlistItems?part=snippet"+query+args);
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4) {
                            if (xhr.status === 200) {
                                try {
                                    var data = JSON.parse(xhr.responseText);

                                    if (!this._handleApiResponse(data, '_getLiveVideos')) {
                                        return;
                                    }

                                    if (data.items && data.items.length > 0) {
                                        var latestVideoIDs = data.items.map(function(item) {
                                            return item.snippet.resourceId.videoId;
                                        }).join(',');
                                        this._checkLiveStatus(latestVideoIDs);
                                    }
                                } catch (e) {
                                    console.error('Failed to parse response for request at index:', i, e);
                                }
                            } else {
                                console.error('HTTP Error for request at index:', i, 'Status:', xhr.status);
                                this.wrapper.dataset.total--;
                            }
                        }
                    }.bind(this);
                    xhr.onerror = function() {
                        console.error('Network Error for request at index:', i);
                    };
                    xhr.send();
                }.bind(this))(i);
            }
        }
    },      
    _getShorts: {
        value: function(channelID) {
            var requests = [];
            var shortsArray = [];
            var requestCount = 0;
        
            for (var i = 0; i < channelID.length; i++) {
                (function(i) {
                    var args = '&maxResults=100';
                    var query = '&playlistId=' + channelID[i];
                    requests[i] = new XMLHttpRequest();
                    requests[i].open("GET", "/?rest_route=/streamweasels-youtube/v1/fetch-videos/"+query+args);
                    requests[i].setRequestHeader("X-WP-Nonce", this.nonce);
                    console.log("_getShorts", "https://www.googleapis.com/youtube/v3/playlistItems?part=snippet"+query+args)
                    requests[i].onreadystatechange = function() {

                        if (requests[i].readyState === 4) {
                            requestCount++;
                            if (requests[i].status === 200) {
                                try {
                                    var data = JSON.parse(requests[i].responseText);

                                    if (!this._handleApiResponse(data, '_getShorts')) {
                                        return;
                                    }

                                    if (data.items && data.items.length > 0) {
                                        var currentShorts = data.items;
                                        shortsArray = shortsArray.concat(currentShorts);
                                    }
                                    // Handle completion and sorting
                                    if (requestCount === channelID.length) {
                                        if (shortsArray.length > 0) {
                                            // Sort and append videos
                                            shortsArray.sort(function(a, b) {
                                                return new Date(b.snippet.publishedAt) - new Date(a.snippet.publishedAt);
                                            });
                                            this._appendVideos(shortsArray);
                                        } else {
                                            // No shorts found
                                            this.showOfflineText = 'No Shorts Found!';
                                            this._postAppend();
                                        }
                                    }
                                } catch (e) {
                                    console.error("Failed to parse response: ", e);
                                }
                            } else {
                                console.error("Request failed with status: ", requests[i].status);
                            }
                        }
                    }.bind(this);
                    requests[i].send();
                }.bind(this))(i);
            }
        }
    },
    _checkLiveStatus: {
        value: function(latestUploads) {
    
            var args = '&id=' + latestUploads;
            var xhr = [];
            xhr[latestUploads] = new XMLHttpRequest();
            xhr[latestUploads].open("GET", "/?rest_route=/streamweasels-youtube/v1/fetch-live/"+args);
            xhr[latestUploads].setRequestHeader("X-WP-Nonce", this.nonce);
            console.log("_checkLiveStatus", "https://www.googleapis.com/youtube/v3/videos"+args);
            xhr[latestUploads].onreadystatechange = function() {
                if (xhr[latestUploads].readyState === 4) {
                    if (xhr[latestUploads].status === 200) {
                        try {
                            var data = JSON.parse(xhr[latestUploads].responseText);

                            if (!this._handleApiResponse(data, '_checkLiveStatus')) {
                                return;
                            }

                            var liveVideoID = null;
    
                            for (var i = 0; i < data.items.length; i++) {
                                if (data.items[i].snippet.liveBroadcastContent === 'live') {
                                    liveVideoID = data.items[i];
                                    this.wrapper.dataset.online++;
                                    break;
                                }
                            }
    
                            // Fallback to the first video if no live video is found
                            if (!liveVideoID && data.items.length > 0) {
                                liveVideoID = data.items[0];
                                this.wrapper.dataset.offline++;
                            }
    
                            if (liveVideoID) {
                                this._appendLiveStreams(liveVideoID);
                            }
                        } catch (e) {
                            console.error('Failed to parse response:', e);
                        }
                    } else {
                        console.error('HTTP Error:', xhr[latestUploads].status);
                        this.wrapper.dataset.total--;
                    }
                }
            }.bind(this);
            xhr[latestUploads].send();
        }
    },          
    _appendVideos:{
        value: function(online) {
            var videoCount = 0;
            this.target.innerHTML = '';
			for (var $i = 0; $i < online.length; $i++) {
				videoCount++;
                var user = online[$i]?.snippet; 
                var thumbnail = user.thumbnails?.maxres?.url || user.thumbnails?.standard?.url || user.thumbnails?.high?.url || user.thumbnails?.medium?.url || user.thumbnails?.default?.url;
                var title = user.title;
                var publishedDate = this._daysAgo(user.publishedAt);
                var videoID = user.resourceId?.videoId;
                var status = online[$i]?.status?.privacyStatus || 'public';
                var channelTitle = this.titleOverride || user.channelTitle || '';
                var channelTitleLower = channelTitle.toLowerCase();
                var language = 'en';
                var type = 'video';
				var logoImage = '';
                if (this.hideShorts && this.shortsIds) {
                    shortsIdArray = (this.shortsIds).split(',');
                    if (shortsIdArray.includes(videoID)) {
                        status = 'private';
                    }
                }

                if (this.logoImage !== '') {
                    logoImage = `<img class="cp-stream__logo" src="${this.logoImage}" style="${this.logoBorderColour && 'border-color:'+this.logoBorderColour};${this.logoBgColour && 'background-color:'+this.logoBgColour}">`
                }                              
				var liveInfo = `
				${logoImage}<div class="cp-stream__info-wrapper">
					<span class="cp-stream__title" style="${this.tileTitleColour && 'color:'+this.tileTitleColour }">${title}</span>
					<span class="cp-stream__meta" style="${this.tileSubtitleColour && 'color:'+this.tileSubtitleColour }"><strong class="cp-stream__meta--game">${channelTitle}</strong> • ${publishedDate}</span>
					</div>
				`            
				var html = `
					<div class="cp-stream cp-stream--online" style="${this.hoverColour && 'background-color:'+this.hoverColour}" data-user="${channelTitleLower}" data-status="${status}">
						<a class="cp-stream__inner" href="https://www.youtube.com/watch?v=${videoID}" target="_blank" title="${title}" data-channel-name="${channelTitleLower}" data-video-id="${videoID}" data-language="${language}" data-type="${type}" data-date="${this._days(user.publishedAt)}" data-status="online" style="${this.tileBgColour && 'background-color:'+this.tileBgColour };${this.tileRoundedCorners && 'border-radius:'+this.tileRoundedCorners+'px' };">
							<div class="cp-stream__image">
								<img loading="lazy" src="${thumbnail}">
								<div class="cp-stream__overlay"></div>
                                <div class="cp-stream__embed-inside"></div>
							</div>
							<div class="cp-stream__info">
								${liveInfo}
							</div>
						</a>
					</div>
				`;
                this.target.insertAdjacentHTML('beforeend', html);
                this.wrapper.dataset.online++;
                if (videoCount == this.limit || videoCount == online.length) {
                    this._postAppend();
                    break;
                }
			};    
		}
    },
    _appendLiveStreams:{
        value: function(online) {
				var user = online.snippet;      
                var liveStatus = user.liveBroadcastContent;
                if (liveStatus == 'live' || this.showOffline) {
                    if ((this.showOffline == true && parseInt(this.wrapper.dataset.total) < this.limit) || (this.showOffline == false && parseInt(this.wrapper.dataset.online) < this.limit)) {
                    var game = user.channelTitle;
                    var language = 'en';
                    if (user.thumbnails.medium) {
                        var thumbnail = user.thumbnails.medium.url;
                    } else {
                        streamWeaselsYouTubeVars.thumbnail
                    }
                    var title = user.title;
                    var type = 'video';
                    var publishedDate = this._daysAgo(user.publishedAt);
                    var userLogin = user.channelTitle;
                    var videoID = online.id;
                    var channelID = user.channelId;
                    var logoImage = '';    
                    if (this.layout == 'status' && liveStatus == 'live') {
                        var publishedDate = this.translationsLive;
                    }
                    if (this.logoImage !== '') {
                        logoImage = `<img class="cp-stream__logo" src="${this.logoImage}" style="${this.logoBorderColour && 'border-color:'+this.logoBorderColour};${this.logoBgColour && 'background-color:'+this.logoBgColour}">`
                    }
                    if (liveStatus == 'live') {
                        liveMeta =  `<span class="cp-stream__meta--live">${this.translationsLive}</span>`;
                    } else {
                        liveMeta =  `• ${publishedDate}`;
                    }  
                    var liveInfo = `
                    ${logoImage}<div class="cp-stream__info-wrapper">
                        <span class="cp-stream__title" style="${this.tileTitleColour && 'color:'+this.tileTitleColour }">${title}</span>
                        <span class="cp-stream__meta" style="${this.tileSubtitleColour && 'color:'+this.tileSubtitleColour }"><strong class="cp-stream__meta--game">${game}</strong> ${liveMeta}</span>
                        </div>
                    `            
                    var html = `
                        <div class="cp-stream cp-stream--${liveStatus}" style="${this.hoverColour && 'background-color:'+this.hoverColour}" data-user="${userLogin.toLowerCase()}">
                            <a class="cp-stream__inner" href="https://www.youtube.com/watch?v=${videoID}" target="_blank" title="${title}" data-channel-name="${userLogin.toLowerCase()}" data-video-id="${videoID}" data-channel-id="${channelID}" data-language="${language}" data-type="${type}" data-date="${this._days(user.publishedAt)}"  data-live="${liveStatus == 'live' ? 1 : 0}" style="${this.tileBgColour && 'background-color:'+this.tileBgColour };${this.tileRoundedCorners && 'border-radius:'+this.tileRoundedCorners+'px' };">
                                <div class="cp-stream__image">
                                    <img loading="lazy" src="${thumbnail}">
                                    <div class="cp-stream__overlay">
                                        <span class="cp-stream__live-status">${this.translationsLive}</span>
                                    </div>
                                    <div class="cp-stream__embed-inside"></div>
                                </div>
                                <div class="cp-stream__info">
                                    ${liveInfo}
                                </div>
                            </a>
                        </div>
                    `;
                    this.target.insertAdjacentHTML('beforeend', html);
                    }
                }
                if (this.wrapper.dataset.total == (parseInt(this.wrapper.dataset.offline, 10) + parseInt(this.wrapper.dataset.online, 10))) {
                    this._postAppend();
                }
		}
    },
    _postAppend:{
        value: function() {
            if (this.loading) {
                this.loading.remove();
            }            
            this._sortStreams();
            if (this.wrapper.dataset.online == 0) {
                this.wrapper.classList.add('cp-streamweasels-youtube--all-offline')
                if (this.showOfflineText || this.showOfflineImage) {
                    this._offlineMessage();       
                }
            } else {
                // this.wrapper.querySelector('.cp-streamweasels-youtube__offline-wrapper').innerHTML = '';
            }    
            if (this.layout == 'feature') {
                if (this.target.children.length) {
                var nodeCount = this.target.querySelectorAll('.cp-stream');
                    if(nodeCount.length == 1) {
                        var node = nodeCount[0];
                        var clone = node.cloneNode(true);
                        this.target.appendChild(clone)
                    }
                setTimeout(function() {
                    startYTFlipster(this.wrapper,this.target)
                }.bind(this), 300)
                }
            }
            if (this.layout == 'showcase') {
                this._startShowcase(this.wrapper,this.target)
            }
            if (this.layout == 'status') {
                this.wrapper.classList.add('cp-streamweasels-youtube--loaded')
                setTimeout(function() {
                    this.target.classList.add('cp-streamweasels-youtube__streams--loaded')
                }.bind(this), 1000)
                setTimeout(function() {
                    this.wrapper.classList.add('cp-streamweasels-youtube--animation-finished')
                }.bind(this), 2000)  
                if (this.target.classList.contains('cp-streamweasels-youtube__streams--carousel-0')) {
                    setTimeout(function() {
                        jQuery(this.target).slick({
                            dots: false,
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            swipeToSlide: true,
                            prevArrow: '<button type="button" class="slick-prev"><svg fill="#FFFFFF" height="24" viewBox="0 0 24 24" width="24"><path d="M15.41 16.09l-4.58-4.59 4.58-4.59L14 5.5l-6 6 6 6z"/><path d="M0-.5h24v24H0z" fill="none"/></svg></button>',
                            nextArrow: '<button type="button" class="slick-next"><svg fill="#FFFFFF" height="24" viewBox="0 0 24 24" width="24"><path d="M8.59 16.34l4.58-4.59-4.58-4.59L10 5.75l6 6-6 6z"/><path d="M0-.25h24v24H0z" fill="none"/></svg></button>',
                        })
                    }.bind(this), 3000)
                }
            }                  
            this._clickHandler();                     
        }
    },    
    _clickHandler:{
        value: function() {
            if (this.autoload) {
                var streams = this.wrapper.querySelectorAll('.cp-stream:not([data-status="private"])')
                if (streams.length > 0) {
                    var featuredStream = streams[0].querySelector('a')
                    this._embedVideo(featuredStream);
                }
            }
            var tiles = this.wrapper.querySelectorAll('.cp-stream__inner');
            tiles.forEach(tile => {
                tile.addEventListener('click', function(e) {
                    e.preventDefault();
                    this._embedVideo(tile);                         
                }.bind(this));
            })
        }
    },
    _embedVideo:{
        value: function(channelNode) {
            var body = document.querySelector('body')
            var modalHtml =
            `<div class="cp-streamweasels-modal">
                <div class="cp-streamweasels-modal__player"><div class="cp-streamweasels-modal__player-target"></div></div>
            </div>`
            var player = this.wrapper.querySelector('.cp-streamweasels-youtube__player');    
            if (this.embed == 'youtube') {
                window.open('https://youtube.com/watch?v='+channelNode.dataset.videoId, '_blank');
            }   
            if (this.layout == 'status' && this.embed == 'page') {
                this.embed = 'popup'
            }                 
            if (this.embed == 'page') {
                var playerWrapper = this.wrapper.querySelector('.cp-streamweasels-youtube__player');
                playerWrapper.innerHTML = '';
                playerWrapper.insertAdjacentHTML('afterbegin', '<div class="cp-streamweasels-youtube__player-target"></div>')
                var player = this.wrapper.querySelector('.cp-streamweasels-youtube__player-target');
                var youtubeEmbed;
               
                // player.innerHTML = '';
                    youtubeEmbed = new YT.Player(player, {
                    height: '390',
                    width: '640',
                    videoId: channelNode.dataset.videoId,
                    playerVars: {
                      'playsinline': 1
                    },
                    events: {
                      'onReady': function(event) {
                        if (this.embedMuted) {
                            event.target.mute();
                        }         
                        if (this.autoplay) {      
                            event.target.playVideo();
                        }
                      }.bind(this),
                      'onStateChange': function() {

                      }.bind(this)
                    }
                  });
            
                this.player.classList.add('cp-streamweasels-youtube__player--active');
          
                // if (this.layout == 'wall') {
                //     player.scrollIntoView();
                // }
                this.wrapper.classList.add('cp-streamweasels-youtube--embed-page-active');
                // this.player.classList.add('cp-streamweasels-youtube__player--embed-page-active');
            }
            if (this.embed == 'popup') {
                body.insertAdjacentHTML('beforeend', modalHtml);
                var wrapper = document.querySelector('.cp-streamweasels-modal')
                var player = document.querySelector('.cp-streamweasels-modal__player-target');
                youtubeEmbed = new YT.Player(player, {
                    height: '390',
                    width: '640',
                    videoId: channelNode.dataset.videoId,
                    playerVars: {
                      'playsinline': 1
                    },
                    events: {
                      'onReady': function(event) {
                        if (this.embedMuted) {
                            event.target.mute();
                        }         
                        if (this.autoplay) {               
                            event.target.playVideo();
                        }
                      }.bind(this),
                      'onStateChange': function() {

                      }.bind(this)
                    }
                  });
                this._modalControls(wrapper);     
                this.wrapper.classList.add('cp-streamweasels-youtube--embed-popup-active');    
            } 
            if (this.layout == 'feature' && this.embed == 'inside') {
                setTimeout(function() {
                    var featureEmbed = this.wrapper.querySelector('.flipster__item--current');
                    var featureEmbedInner = featureEmbed.querySelector('.cp-stream__image');
                    var featureEmbedInnerInside = featureEmbed.querySelector('.cp-stream__embed-inside');
                    var featureEmbedIframe = featureEmbedInner.querySelector('iframe')
                    var featureEmbedImage = featureEmbedInner.querySelector('img');
                    var featureEmbedInnerWidth = featureEmbedImage.width
                    var featureEmbedInnerHeight = featureEmbedImage.height
                    if (this.tileLayout == 'detailed') {
                        featureEmbedInnerHeight = featureEmbedInnerHeight + 48;
                    }
                    featureEmbed.classList.add('flipster__item--embed')
                    if (featureEmbedIframe) {
                        featureEmbedIframe.remove()
                    }
                    youtubeEmbed = new YT.Player(featureEmbedInnerInside, {
                        height: featureEmbedInnerHeight,
                        width: featureEmbedInnerWidth,
                        videoId: channelNode.dataset.videoId,
                        playerVars: {
                        'playsinline': 1
                        },
                        events: {
                        'onReady': function(event) {
                            if (this.embedMuted) {
                                event.target.mute();
                            }         
                            event.target.playVideo();
                        }.bind(this),
                        'onStateChange': function() {

                        }.bind(this)
                        }
                    });                
                    this.wrapper.classList.add('cp-streamweasels-youtube--embed-page-active');
                    this.embedTitle = false;
                }.bind(this), 500)
            }
            if (this.layout == 'showcase' && this.embed == 'inside' && !channelNode.classList.contains('slick__item--embed')) {
                var featureEmbed = channelNode;
                var featureEmbedInner = featureEmbed.querySelector('.cp-stream__image');
                var featureEmbedInnerInside = featureEmbed.querySelector('.cp-stream__embed-inside');
                var featureEmbedIframe = featureEmbedInner.querySelector('iframe')
                var featureEmbedImage = featureEmbedInner.querySelector('img');
                channelNode.classList.add('slick__item--embed')
                if (featureEmbedIframe) {
                    featureEmbedIframe.remove()
                }
                youtubeEmbed = new YT.Player(featureEmbedInnerInside, {
                    height: '100%',
                    width: '100%',
                    videoId: channelNode.dataset.videoId,
                    playerVars: {
                      'playsinline': 1
                    },
                    events: {
                      'onReady': function(event) {
                        if (this.embedMuted) {
                            event.target.mute();
                        }         
                        event.target.playVideo();
                      }.bind(this),
                      'onStateChange': function() {

                      }.bind(this)
                    }
                  });                
                this.wrapper.classList.add('cp-streamweasels-youtube--embed-page-active');
                this.embedTitle = false;
            }                          
            if (this.embedTitle && this.embed == 'page' && channelNode.dataset.status == 'online') {
                this._embedTitle(channelNode.getAttribute('title'))
            }
        }
    },   
    _sortStreams:{
        value: function() {
            var streams = this.wrapper.querySelector('.cp-streamweasels-youtube__streams');
            [...streams.children]
                .sort(function(a,b) {
                    if (this.liveStreamID && this.wrapper.dataset.online > 0) {
                        return (a.children[0].dataset.live == 0 ? 1: -1);
                    }                     
                    if (this.tileSorting == 'alpha') {
                        return (a.children[0].getAttribute('title')>b.children[0].getAttribute('title')? 1: -1);
                    }                    
                    if (this.tileSorting == 'least') {
                        return b.children[0].dataset.date - a.children[0].dataset.date;
                    }
                    if (this.tileSorting == 'most') {
                        return a.children[0].dataset.date - b.children[0].dataset.date;
                    }      
                    if (this.tileSorting == 'random') {
                        return 0.5 - Math.random()
                    }                                   
                }.bind(this))
                .forEach(node=> {
                    streams.appendChild(node)
                });
        }
    },
    _offlineMessage: {
        value: function() {
            var offlineHTML =
            `<div class="cp-streamweasels-youtube__offline">
                ${this.showOfflineImage && "<img src='"+this.showOfflineImage+"'>"}
                ${this.showOfflineText && "<h3>"+this.showOfflineText+"</h3>"}
            </div>`;
            this.wrapper.querySelector('.cp-streamweasels-youtube__offline-wrapper').innerHTML = '';
            this.wrapper.querySelector('.cp-streamweasels-youtube__offline-wrapper').insertAdjacentHTML('beforeend', offlineHTML)
        }
    },
    _updatePagination: {
        value: function(nextPageToken, prevPageToken, uploadsID) {
            this.wrapper.querySelector('.cp-streamweasels-youtube__pagination').innerHTML = '';
            if (prevPageToken) {
                this.wrapper.dataset.prevPageToken = prevPageToken;
                this.wrapper.querySelector('.cp-streamweasels-youtube__pagination').insertAdjacentHTML('beforeend', '<a class="cp-streamweasels-youtube__pagination--prev" href="#" data-prev="'+prevPageToken+'" data-uuid="'+this.uuid+'" data-playlist="'+uploadsID+'">❮ '+this.translationsPrevPage+'</a>')
                this.wrapper.querySelector('.cp-streamweasels-youtube__pagination--prev').onclick = function(e) { YTGetNextPage(e) };
            }
            if (nextPageToken) {
                this.wrapper.dataset.nextPageToken = nextPageToken;
                this.wrapper.querySelector('.cp-streamweasels-youtube__pagination').insertAdjacentHTML('beforeend', '<a class="cp-streamweasels-youtube__pagination--next" href="#" data-next="'+nextPageToken+'" data-uuid="'+this.uuid+'" data-playlist="'+uploadsID+'">'+this.translationsNextPage+' ❯</a>')
                this.wrapper.querySelector('.cp-streamweasels-youtube__pagination--next').onclick = function(e) { YTGetNextPage(e) };
            }            
        }
    },
    _modalControls:{
        value: function(modal) {
            modal.addEventListener('click', function(e) {
                modal.remove();
            })
            document.onkeydown = function(e){
                if(e.key === 'Escape'){
                    modal.remove();
                }
            }
        }
    },    
    _daysAgo:{
        value: function(date) {
            const now = new Date();
            // Mimick a backend date
            var daysAgo = new Date(date);
            daysAgo.setDate(daysAgo.getDate());
            // Compare both, outputs in miliseconds
            var ago = now - daysAgo;
            var ago = Math.floor(ago / 1000);
            var part = 0;
            if (ago < 2) { return "a moment ago"; }
            if (ago < 5) { return "moments ago"; }
            if (ago < 60) { return ago + " seconds ago"; }    
            if (ago < 120) { return "a minute ago"; }
            if (ago < 3600) {
              while (ago >= 60) { ago -= 60; part += 1; }
              return part + " minutes ago";
            }
            if (ago < 7200) { return "an hour ago"; }
            if (ago < 86400) {
              while (ago >= 3600) { ago -= 3600; part += 1; }
              return part + " hours ago";
            }    
            if (ago < 172800) { return "a day ago"; }
            if (ago < 604800) {
              while (ago >= 172800) { ago -= 172800; part += 1; }
              return part + " days ago";
            }
            if (ago < 1209600) { return "a week ago"; }
            if (ago < 2592000) {
              while (ago >= 604800) { ago -= 604800; part += 1; }
              return part + " weeks ago";
            }
            if (ago < 5184000) { return "a month ago"; }
            if (ago < 31536000) {
              while (ago >= 2592000) { ago -= 2592000; part += 1; }
              return part + " months ago";
            }
            if (ago < 1419120000) { // 45 years, approximately the epoch
              return "more than year ago";
            }
        }
    },
    _days:{
        value: function(date) {
            const now = new Date();
            // Mimick a backend date
            var daysAgo = new Date(date);
            daysAgo.setDate(daysAgo.getDate());
            // Compare both, outputs in miliseconds
            var ago = now - daysAgo;
            var ago = Math.floor(ago / 1000);
            return ago;
        }
    },
    _startShowcase:{
        value: function(wrapper,target) {         

            jQuery(window).resize(function(){
                jQuery(target).slick('slickSetOption','slidesToShow', this._slidesToShow(wrapper), true);

            }.bind(this));

            jQuery(target).slick({
                dots: false,
                slidesToShow: this._slidesToShow(wrapper),
                slidesToScroll: 1,
                swipeToSlide: true,
                prevArrow: '<button type="button" class="slick-prev"><svg fill="#FFFFFF" height="24" viewBox="0 0 24 24" width="24"><path d="M15.41 16.09l-4.58-4.59 4.58-4.59L14 5.5l-6 6 6 6z"/><path d="M0-.5h24v24H0z" fill="none"/></svg></button>',
                nextArrow: '<button type="button" class="slick-next"><svg fill="#FFFFFF" height="24" viewBox="0 0 24 24" width="24"><path d="M8.59 16.34l4.58-4.59-4.58-4.59L10 5.75l6 6-6 6z"/><path d="M0-.25h24v24H0z" fill="none"/></svg></button>',
            })
        }
    }, 
    _slidesToShow:{
        value: function(wrapper) {
            if (wrapper.offsetWidth >= 1280) {
                slidesToShow = this.slideCount ? this.slideCount : 6
            } else if (wrapper.offsetWidth >= 1024) {
                slidesToShow = this.slideCount ? this.slideCount : 5
            } else if (wrapper.offsetWidth >= 768) {
                slidesToShow = this.slideCount ? this.slideCount : 4
            } else if (wrapper.offsetWidth >= 560) {
                slidesToShow = 3;
            } else if (wrapper.offsetWidth >= 380) {
                slidesToShow = 2;
            } else {
                slidesToShow = 1;
            }       
            return slidesToShow  
        }
    }
})

function swyiFetchFreshNonce() {
    return fetch(streamWeaselsYouTubeVars.ajaxUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=swyi_get_fresh_nonce'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('fetchFreshNonce() failed');
        }
        return response.json();
    })
    .then(data => {
        if (data.success && data.data && data.data.nonce) {
            return data.data.nonce;
        } else {
            throw new Error('fetchFreshNonce() success - data not valid: ' + JSON.stringify(data));
        }
    });
}

var streamWeaselsYouTubeNodes = document.querySelectorAll('.cp-streamweasels-youtube');
streamWeaselsYouTubeNodes.forEach(function(item, index, array) {
    var uuid = item.dataset.uuid;
    swyiFetchFreshNonce().then(function(freshNonce) { 
        var streamWeaselsYouTubeVarUuid = eval('streamWeaselsYouTubeVars'+uuid);
        var streamWeaselsYouTubeInit = new streamWeaselsYouTube({
        uuid: uuid,
        channelID: streamWeaselsYouTubeVarUuid.YouTubeChannelID,
        playlistID: streamWeaselsYouTubeVarUuid.YouTubePlaylistID,
        pageToken: false,
        liveStreamID: streamWeaselsYouTubeVarUuid.YouTubeLiveStreamID,
        titleOverride: streamWeaselsYouTubeVarUuid.titleOverride,
        limit: streamWeaselsYouTubeVarUuid.limit,
        layout: streamWeaselsYouTubeVarUuid.layout,
        slideCount: streamWeaselsYouTubeVarUuid.slideCount,
        pagination: streamWeaselsYouTubeVarUuid.pagination,
        embed: streamWeaselsYouTubeVarUuid.embed,    
        embedMuted: streamWeaselsYouTubeVarUuid.embedMuted,   
        showOffline: streamWeaselsYouTubeVarUuid.showOffline,
        showOfflineText: streamWeaselsYouTubeVarUuid.showOfflineText,
        showOfflineImage: streamWeaselsYouTubeVarUuid.showOfflineImage,        
        autoload: streamWeaselsYouTubeVarUuid.autoload, 
        autoplay: streamWeaselsYouTubeVarUuid.autoplay, 
        logoImage: streamWeaselsYouTubeVarUuid.logoImage,
        logoBgColour: streamWeaselsYouTubeVarUuid.logoBgColour,
        logoBorderColour: streamWeaselsYouTubeVarUuid.logoBorderColour,
        tileSorting: streamWeaselsYouTubeVarUuid.tileSorting,
        tileBgColour: streamWeaselsYouTubeVarUuid.tileBgColour,
        tileTitleColour: streamWeaselsYouTubeVarUuid.tileTitleColour,
        tileSubtitleColour: streamWeaselsYouTubeVarUuid.tileSubtitleColour,
        hoverColour: streamWeaselsYouTubeVarUuid.hoverColour,
        enableCache: streamWeaselsYouTubeVarUuid.enableCache,
        hideShorts: streamWeaselsYouTubeVarUuid.hideShorts,
        shortsIds: streamWeaselsYouTubeVarUuid.shortsIds,
        translationsLive: streamWeaselsYouTubeVarUuid.translationsLive,
        translationsViews: streamWeaselsYouTubeVarUuid.translationsViews,
        translationsNextPage: streamWeaselsYouTubeVarUuid.translationsNextPage,
        translationsPrevPage: streamWeaselsYouTubeVarUuid.translationsPrevPage,
        nonce: freshNonce
    })
}).catch(function(error) {
    console.error('Error fetching nonce:', error);
});
})

function YTGetNextPage(e) {
    e.preventDefault();
    var uuid = e.target.dataset.uuid;
    var streamWeaselsYouTubeVarUuid = eval('streamWeaselsYouTubeVars'+uuid);
    var pageToken = e.target.dataset.next || e.target.dataset.prev;
    var playlist = e.target.dataset.playlist;

    var streamWeaselsYouTubeInit = new streamWeaselsYouTube({
        uuid: uuid,
        playlistID: playlist,
        pageToken: pageToken,
        limit: streamWeaselsYouTubeVarUuid.limit,
        pagination: streamWeaselsYouTubeVarUuid.pagination,
        embed: streamWeaselsYouTubeVarUuid.embed,    
        embedMuted: streamWeaselsYouTubeVarUuid.embedMuted,   
        showOffline: streamWeaselsYouTubeVarUuid.showOffline,
        showOfflineText: streamWeaselsYouTubeVarUuid.showOfflineText,
        showOfflineImage: streamWeaselsYouTubeVarUuid.showOfflineImage,        
        autoload: streamWeaselsYouTubeVarUuid.autoload, 
        autoplay: streamWeaselsYouTubeVarUuid.autoplay, 
        logoImage: streamWeaselsYouTubeVarUuid.logoImage,
        logoBgColour: streamWeaselsYouTubeVarUuid.logoBgColour,
        logoBorderColour: streamWeaselsYouTubeVarUuid.logoBorderColour,
        tileSorting: streamWeaselsYouTubeVarUuid.tileSorting,
        tileBgColour: streamWeaselsYouTubeVarUuid.tileBgColour,
        tileTitleColour: streamWeaselsYouTubeVarUuid.tileTitleColour,
        tileSubtitleColour: streamWeaselsYouTubeVarUuid.tileSubtitleColour,
        hoverColour: streamWeaselsYouTubeVarUuid.hoverColour,
        enableCache: streamWeaselsYouTubeVarUuid.enableCache,
        hideShorts: streamWeaselsYouTubeVarUuid.hideShorts,
        shortsIds: streamWeaselsYouTubeVarUuid.shortsIds,
        translationsLive: streamWeaselsYouTubeVarUuid.translationsLive,
        translationsViews: streamWeaselsYouTubeVarUuid.translationsViews,
        translationsNextPage: streamWeaselsYouTubeVarUuid.translationsNextPage,
        translationsPrevPage: streamWeaselsYouTubeVarUuid.translationsPrevPage,
        nonce: streamWeaselsYouTubeVarUuid.nonce    
    })
}

function startYTFlipster(wrapper,target) {
	var numberOnline = target.querySelectorAll('.cp-stream--live').length;
	var startAt = 2;
	switch(numberOnline) {
		case 1:
			startAt = 0;
			break;
		case 2:
			startAt = 1;
			break;
	}
	jQuery(wrapper).flipster({
		style: 'carousel',
		itemContainer: '.cp-streamweasels-youtube__streams',
		itemSelector: '.cp-stream',
		loop: true,
		buttons: true,
		spacing: -0.5,
		scrollwheel: false,
		start: startAt,
		onItemSwitch: function() {
			var activeEmbed = wrapper.querySelector('.flipster__item--embed')
			var slidePast = wrapper.querySelector('.flipster__item--past-1')
			var slideFuture = wrapper.querySelector('.flipster__item--future-1')
			if (activeEmbed) {
				var activeiFrame = activeEmbed.querySelector('iframe')
                if (slidePast) {
                    var activeiPast = slidePast.querySelector('iframe')
                }
                if (slideFuture) {
                    var activeiFuture = slideFuture.querySelector('iframe')
                }
				activeEmbed.classList.remove('flipster__item--embed')
				if (activeiFrame) {
                    activeiFrame.insertAdjacentHTML('beforebegin', '<div class="cp-stream__embed-inside"></div>');
					activeiFrame.remove()
				}
				if (activeiPast) {
                    activeiFrame.insertAdjacentHTML('beforebegin', '<div class="cp-stream__embed-inside"></div>');
					activeiFrame.remove()
				}	
				if (activeiFuture) {
                    activeiFrame.insertAdjacentHTML('beforebegin', '<div class="cp-stream__embed-inside"></div>');
					activeiFrame.remove()
				}							
			}
		}
	});
}
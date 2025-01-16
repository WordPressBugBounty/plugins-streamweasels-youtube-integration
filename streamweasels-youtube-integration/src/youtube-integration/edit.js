import {
	PanelBody,
	PanelRow,
	TextControl,
	QueryControls,
	ToggleControl,
	SelectControl,
	RangeControl
} from '@wordpress/components';

import { useState } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';

/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit({ attributes, setAttributes }) {

	const { 
		layout,
		channels,
		playlist,
		livestream,
		language,
		limit
	} = attributes;

	let channelsArray = [];

	// If game is set, add it to the array
	channelsArray = channels ? (() => {
		const newArray = [];
		for (let i = 0; i < limit; i++) {
			newArray.push(channels);
		}
		return newArray;
	})() : channelsArray;

	// If game is set, add it to the array
	channelsArray = playlist ? (() => {
		const newArray = [];
		for (let i = 0; i < limit; i++) {
			newArray.push(playlist);
		}
		return newArray;
	})() : channelsArray;	
	
	// If game is set, add it to the array
	if (livestream) {
		channelsArray = livestream.split(',');
	}

	let dataType= channels ? 'channel' : (playlist ? 'playlist' : (livestream ? 'livestream' : null));

	const [data, setData] = wp.element.useState([]);
    const [currentSlide, setCurrentSlide] = useState(0);

    const handlePrevious = () => {
        setCurrentSlide((prevSlide) => (prevSlide - 1 + channelsArray.length) % channelsArray.length);
    };

    const handleNext = () => {
        setCurrentSlide((prevSlide) => (prevSlide + 1) % channelsArray.length);
    };

	wp.apiFetch({ path: '/streamweasels-youtube/v1/data' }).then(response => {
		setData(response);
	});

	let dateTimestamp1, dateTimestamp2, connectionExpiresMeta, licenseStatusColour, licenseStatusLabel, spanStyle, layoutArray; // Declare the variables outside the if statement

	if (data.accessToken) {
		licenseStatusColour = 'green';
		licenseStatusLabel = 'YouTube API Connected!';
	} else {
		licenseStatusColour = 'gray';
		licenseStatusLabel = 'Not Connected';											
	}	

	if (data.connectionExpires) {
	  connectionExpiresMeta = '(expires on ' + data.connectionExpires + ')';
	  dateTimestamp1 = new Date(data.connectionExpires).getTime();
	  dateTimestamp2 = new Date().setHours(0, 0, 0, 0);
	}
	
	if (data.connectionExpires && dateTimestamp2 > dateTimestamp1) {
	  licenseStatusColour = 'red';
	  licenseStatusLabel = 'YouTube API Connection Expired!';
	  connectionExpiresMeta = '(expired on ' + data.connectionExpires + ')';
	}		

	if (data.accessTokenErrorCode) {
		licenseStatusColour = 'red';
		licenseStatusLabel = 'YouTube API Connection Error!';
		connectionExpiresMeta = '('+data.accessTokenErrorMessage+')';
	}

	if (data.proStatus) {
		layoutArray = [
			{label: 'Wall', value: 'wall'},
			{label: 'Player', value: 'player'},
			{label: 'Status', value: 'status'},
			{label: 'Showcase', value: 'showcase'},
			{label: 'Feature', value: 'feature'},
		];
	} else {
		layoutArray = [
			{label: 'Wall', value: 'wall'},
			{label: 'Player', value: 'player'},
			{label: 'Status', value: 'status'},
			{label: 'Showcase', value: 'showcase'},
		];
	}

	spanStyle = {
		color: licenseStatusColour,
		fontWeight: 'bold',
	  };

	return (
		<>
		<InspectorControls>		
			<PanelBody title={ __( 'YouTube API Connection', 'streamweasels' ) }>
				<PanelRow>
					<div>
						<p style={spanStyle}>{licenseStatusLabel}</p>
						<p style={spanStyle}>{connectionExpiresMeta}</p>
						{licenseStatusColour !== 'green' && ( <p>Your YouTube API Connection needs attention! Check out the <a href="admin.php?page=streamweasels-youtube" target="_blank">YouTube Integration Settings</a> for more information.</p> )}
					</div>
				</PanelRow>
			</PanelBody>
			<PanelBody title={ __( 'YouTube Integration Settings', 'streamweasels' ) }>
				<PanelRow>
					<SelectControl
						label={ __(
							'Layout',
							'streamweasels'
						) }
						help={ __(
							'Choose the desired layout for your streams.',
							'streamweasels'
						) }						
						value={ layout }
						onChange={ ( layout ) =>
							setAttributes( { layout: layout } )
						}
						options={ layoutArray }
					/>
				</PanelRow>			
				<div>
					<p><a href="https://www.streamweasels.com/youtube-wordpress-plugins/" target="_blank">Click Here</a> for detailed examples of each layout.</p>
				</div>					
				<PanelRow>
					<TextControl
						label={ __(
							'Channel ID',
							'streamweasels'
						) }
						help={ __(
							'Enter the YouTube channel ID you want to display.',
							'streamweasels'
						) }
						value={ channels }
						onChange={ ( channels ) => setAttributes( { channels: channels} ) }
					/>
				</PanelRow>	
				<PanelRow>
					<TextControl
						label={ __(
							'Playlist ID',
							'streamweasels'
						) }
						help={ __(
							'Enter the YouTube playlist ID you want to display.',
							'streamweasels'
						) }
						value={ playlist }
						onChange={ ( playlist ) => setAttributes( { playlist: playlist} ) }
					/>
				</PanelRow>	
				<PanelRow>
					<TextControl
						label={ __(
							'Livestream ID',
							'streamweasels'
						) }
						help={ __(
							'Enter the YouTube livestream ID you want to display.',
							'streamweasels'
						) }
						value={ livestream }
						onChange={ ( livestream ) => setAttributes( { livestream: livestream} ) }
					/>
				</PanelRow>		
				<PanelRow>
					<div>
						<RangeControl 
							label="Number of Streams"
							help={ __(
								'Enter the number of streams to display.',
								'streamweasels'
							) }							
							value={ limit }
							onChange={ ( value ) => setAttributes( { limit: value } ) }
							min={ 1 }
							max={ 50 }
						/>
					</div>
				</PanelRow>																										
			</PanelBody>
			<PanelBody title={ __( 'YouTube Advanced Settings', 'streamweasels' ) }>
				<PanelRow>
					<div>
						<p>Looking to customise your YouTube Integration even further? Check out the <a href="admin.php?page=streamweasels" target="_blank">YouTube Integration Settings</a> page for more options.</p>
					</div>
				</PanelRow>
			</PanelBody>			
		</InspectorControls>
					
        <div { ...useBlockProps() }>	
			{licenseStatusColour == 'red' && (
				<div className="cp-swyi__error">
					<p>Your YouTube API Connection needs attention! Check out the <a href="admin.php?page=streamweasels-youtube" target="_blank">YouTube Integration Settings</a> for more information.</p>
				</div>
			)}
			{channelsArray.length ? (
            <div className="cp-swyi" data-colour="light" data-columns="4" data-layout={layout}>
				{layout === 'wall' && (
					<>
						{channelsArray.slice(0, limit).map((channel, index) => (
							<div key={index} className="cp-swyi__stream" data-type={dataType}>
								{dataType}
							</div>
						))}
					</>
				)}
				{layout === 'player' && (
					<>
						<div className="cp-swyi__player-wrapper">
							<div className="cp-swyi__player">
								<p>Embedded Player</p>
							</div>
						</div>
						<div className="cp-swyi__player-list">
							{channelsArray.slice(0, limit).map((channel, index) => (
								<div key={index} className="cp-swyi__stream" data-type={dataType}>
									{dataType}
								</div>
							))}
						</div>
					</>
				)}	
				{layout === 'status' && (
					<>
						<div className="cp-swyi__twitch-logo">
							<span class="dashicon dashicons dashicons-youtube"></span>
						</div>
						<div className="cp-swyi__player-list">
							<div className="cp-swyi__stream" data-type={dataType}>
								<p><strong>Livestream</strong></p>
								<p>Streaming X for 100 Viewers.</p>
							</div>
						</div>
					</>
				)}	
				{layout === 'feature' && (
					<>
					<button className="cp-swyi__arrow cp-swyi__arrow-left" onClick={handlePrevious}>←</button>
					<button className="cp-swyi__arrow cp-swyi__arrow-right" onClick={handleNext}>→</button>
					<div className="cp-swyi__container" style={{ transform: `translateX(-${(currentSlide * 33.33)}%)` }}>
						{channelsArray.slice(0, limit).map((channel, index) => (
							<div key={index} className={`cp-swyi__stream ${index === currentSlide + 1 ? 'active' : ''}`}>
								{dataType}
							</div>
						))}
					</div>
				</>
				)}
				{layout === 'showcase' && (
					<>
					<button className="cp-swyi__arrow cp-swyi__arrow-left" onClick={handlePrevious}>←</button>
					<button className="cp-swyi__arrow cp-swyi__arrow-right" onClick={handleNext}>→</button>
					<div className="cp-swyi__container" style={{ transform: `translateX(-${(currentSlide * 25)}%)` }}>
						{channelsArray.slice(0, limit).map((channel, index) => (
							<div key={index} className="cp-swyi__stream" data-type={dataType}>
								SHORTS
							</div>
						))}
					</div>
					</>
				)}																							
            </div>
		) : (
			<div className="cp-swyi__empty">
				<p>Enter YouTube Channel, Playlist ID or Livestream IDs.</p>
			</div>
		)}
        </div>
		</>
	);
}

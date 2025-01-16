import {
	PanelBody,
	PanelRow,
	TextControl,
	QueryControls,
	ToggleControl,
	SelectControl,
	RangeControl,
	BaseControl,
	FormToggle
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
		id,
		autoplay,
		muted,
		embedChat,
		type,
		width,
		height
	} = attributes;

	let iframeWidth, iframeHeight = '100%';
	let widthPlaceholder;

	if (!width.endsWith('px') && !width.endsWith('%')) {
		iframeWidth = width + 'px';
	  } else {
		iframeWidth = width ? width : iframeWidth;
	  }
	if (!height.endsWith('px') && !height.endsWith('%')) {
		iframeHeight = height + 'px';
	} else {
		iframeHeight = height ? height : iframeHeight;
	  }  

	  if (type === 'video') {
		widthPlaceholder = '100%';
		if (width == '') {
			iframeWidth = '100%';
		}
	} else {
		widthPlaceholder = '480px';
		if (width == '') {
			iframeWidth = '480px';
		}		
	}	  


	return (
		<>
		<InspectorControls>		
			<PanelBody title={ __( 'Twitch Embed Settings', 'streamweasels' ) }>
				<TextControl
					type="text"
					name="id"
					label={ __( 'Video ID', 'streamweasels' ) }
					help={ __(
						'Add the Video ID to embed. You can find this ID on the end of every YouTube video page.',
						'streamweasels'
					) }						
					placeholder={ __( 'aqz-KE-bpKQ', 'streamweasels' ) }
					value={ id }
					onChange={ ( content ) =>
						setAttributes( { id: content } )
					}
				/>	
				<PanelRow>
					<SelectControl
						label={ __(
							'Video Type',
							'streamweasels'
						) }
						help={ __(
							'Choose which type of YouTube content to embed.',
							'streamweasels'
						) }						
						value={ type }
						onChange={ ( type ) =>
							setAttributes( { type: type } )
						}
						options={[{label: 'Video', value: 'video'},{label: 'Short', value: 'short'}]}
					/>
				</PanelRow>				
				<TextControl
					type="text"
					name="width"
					label={ __( 'Embed Width', 'streamweasels' ) }
					placeholder={ __( widthPlaceholder, 'streamweasels' ) }
					help={ __(
						'Leave this blank to default to 100% width.',
						'streamweasels'
					) }					
					value={ width }
					onChange={ ( content ) =>
						setAttributes({ width: content !== '' ? content : '' })
					}
				/>
				<TextControl
					type="text"
					name="height"
					label={ __( 'Embed Height', 'streamweasels' ) }
					placeholder={ __( '100%', 'streamweasels' ) }
					help={ __(
						'Leave this blank to default to 100% height.',
						'streamweasels'
					) }						
					value={ height }
					onChange={ ( content ) =>
						setAttributes({ height: content !== '' ? content : '' })
					}
				/>															
			</PanelBody>
			<PanelBody title={ __( 'YouTube Advanced Settings', 'streamweasels' ) }>
				<PanelRow>
					<div>
						<p>Looking to customise your YouTube Integration even further? Check out the <a href="admin.php?page=streamweasels-youtube" target="_blank">YouTube Integration Settings</a> page for more options.</p>
					</div>
				</PanelRow>
			</PanelBody>						
		</InspectorControls>
					
        <div { ...useBlockProps() }>	
			<div className="cp-swti__embed" data-colour="light" data-aspect-ratio={type} style={{width: iframeWidth, height: iframeHeight}}>
				<div>
					<span className="cp-swti__embed--play"></span>
					<span className="cp-swti__embed--id">{id}</span>
				</div>
			</div>
        </div>
		</>
	);
}

( function( blocks, element, editor, components, i18n, apiFetch ) {
    var el = element.createElement;
    var useEffect = element.useEffect;
    var InspectorControls = editor.InspectorControls;
    var TextControl = components.TextControl;
    var SelectControl = components.SelectControl;
    var useState = element.useState;

    blocks.registerBlockType( 'bluebillywig/embed', {
        title: i18n.__( 'Blue Billywig Embed', 'bluebillywig' ),
        icon: {
            src: el('svg',
                {
                    xmlns: "http://www.w3.org/2000/svg",
                    viewBox: "0 0 75.11811 86.45669"
                },
                el('g', { id: "Logo" },
                    el('g', {},
                        el('path', { d: "M24.82413,52.57589c6.35577,0,13.26892-.32634,19.09185-3.89251,6.19211-3.79248,10.35236-11.33027,10.35236-18.757h-4.42897c0,5.94278-3.31001,11.96272-8.23666,14.98015-5.40688,3.3114-12.26213,3.27136-18.8922,3.23202-.70947-.00405-1.41363-.00823-2.1104-.00823v4.42897c.68826,0,1.38364,.00405,2.08431,.00823,.70458,.00419,1.41838,.00837,2.1397,.00837Z", style: { fill: "#002837" } }),
                        el('rect', { x: "20.60011", y: "60.41784", width: "29.2285", height: "4.2861", style: { fill: "#002837" } }),
                        el('path', { d: "M40.73463,21.32206v4.26575c0,10.79297-6.04199,13.00439-15.11426,13.00439h-5.0025v4.5h5.0025c4.85107,0,19.61426,0,19.61426-17.50439v-4.26575h-4.5Z", style: { fill: "#649bd2" } })
                    )
                )
            ),
        },
        category: 'embed',
        attributes: {
            videoId: {
                type: 'string',
                default: ''
            },
            customPlayoutScreen: {
                type: 'string',
                default: 'default'
            },
            publication: {
                type: 'string',
                default: 'plugindemo'
            },
            mediaClips: {
                type: 'array',
                default: []
            },
            searchQuery: {
                type: 'string',
                default: ''
            }
        },
        edit: function( props ) {
            var { attributes, setAttributes, className } = props;
            var { videoId, customPlayoutScreen, publication, mediaClips, searchQuery } = attributes;

            // State to manage loading and errors
            var [ isLoading, setIsLoading ] = useState( true );
            var [ fetchError, setFetchError ] = useState( null );
            var [ playoutOptions, setPlayoutOptions ] = useState( [] );

            // Fetch the default playout options on component mount
            useEffect( () => {
                apiFetch({
                    path: '/bluebillywig/v1/get_custom_playout_screen',
                    method: 'GET'
                }).then((data) => {
                    console.log('Fetched data:', data);
                    setAttributes({
                        customPlayoutScreen: data.customPlayoutScreen || 'default',
                        publication: data.publication || 'plugindemo'
                    });

                    // Set playout options for the dropdown
                    setPlayoutOptions([
                        { label: 'Default', value: 'default' },
                        { label: 'Playout 1', value: 'playout1' }, // Example values
                        { label: 'Playout 2', value: 'playout2' }  // Example values
                    ]);

                    setIsLoading( false );
                }).catch((error) => {
                    console.error('Error fetching customPlayoutScreen and publication:', error);
                    setFetchError( 'Failed to fetch configuration. Using default values.' );
                    setIsLoading( false );
                });
            }, [] );

            // Handle search input changes
            function onSearchQueryChange( newQuery ) {
                setAttributes({ searchQuery: newQuery });

                if ( newQuery.length > 2 ) {
                    apiFetch({
                        path: `/bluebillywig/v1/search_media_clips?search=${newQuery}`,
                        method: 'GET'
                    }).then((data) => {
                        setAttributes({ mediaClips: data });
                    }).catch((error) => {
                        console.error('Error searching media clips:', error);
                    });
                }
            }

            return [
                el(
                    InspectorControls,
                    { key: 'inspector' },
                    el(
                        SelectControl,
                        {
                            label: i18n.__( 'Choose Playout', 'bluebillywig' ),
                            value: customPlayoutScreen,
                            options: playoutOptions,
                            onChange: (value) => setAttributes({ customPlayoutScreen: value }),
                        }
                    ),
                    el(
                        TextControl,
                        {
                            label: i18n.__( 'Search for Media Clip', 'bluebillywig' ),
                            value: searchQuery,
                            onChange: onSearchQueryChange,
                            help: fetchError ? fetchError : ''
                        }
                    )
                ),
                el(
                    'div',
                    { className: className },
                    isLoading ?
                        el( 'p', {}, i18n.__( 'Loading configuration...', 'bluebillywig' ) ) :
                        searchQuery.length > 2 ?
                            mediaClips.map((clip, index) =>
                                el( 'div', { key: index, className: 'media-clip-result' },
                                    el( 'p', {}, clip.title ),
                                    el( 'button', {
                                        onClick: () => setAttributes({ videoId: clip.id })
                                    }, i18n.__( 'Select this clip', 'bluebillywig' ))
                                )
                            ) :
                            videoId ?
                                el( 'iframe', {
                                    src: `https://${publication}.bbvms.com/p/${customPlayoutScreen}/c/${videoId}.html?inheritDimensions=true&placementOption=default#!referrer=${encodeURIComponent(location.href)}&realReferrer=${encodeURIComponent(document.referrer)}`,
                                    width: '100%',
                                    height: '400',
                                    frameBorder: '0',
                                    allow: 'autoplay; fullscreen',
                                    allowFullScreen: true
                                } ) :
                                el( 'p', {}, i18n.__( 'Please enter a Video ID or search for a clip.', 'bluebillywig' ) )
                )
            ];
        },
        save: function( props ) {
            var { attributes, className } = props;
            var { videoId, customPlayoutScreen, publication } = attributes;

            if ( !videoId ) {
                return null;
            }

            return el(
                'div',
                { className: className },
                el( 'iframe', {
                    src: `https://${publication}.bbvms.com/p/${customPlayoutScreen}/c/${videoId}.html?inheritDimensions=true&placementOption=default#!referrer=${encodeURIComponent(location.href)}&realReferrer=${encodeURIComponent(document.referrer)}`,
                    width: '100%',
                    height: '400',
                    frameBorder: '0',
                    allow: 'autoplay; fullscreen',
                    allowFullScreen: true
                } )
            );
        }
    });
}(
    window.wp.blocks,
    window.wp.element,
    window.wp.blockEditor,
    window.wp.components,
    window.wp.i18n,
    window.wp.apiFetch
) );

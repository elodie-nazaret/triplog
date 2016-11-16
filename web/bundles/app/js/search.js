$(function() {
    var algoliaManager = {

        client: null,
        helper: null,

        init: function(applicationId, apiKey, indexName, params) {
            this.client = algoliasearch(applicationId, apiKey);
            this.helper = algoliasearchHelper(this.client, indexName, params);
            this.helper.on('result', this.displayResults);
        },

        search: function() {
            var query = domManager.searchInput.val();
            this.helper.setQuery(query);
            this.helper.search();
        },

        displayResults: function(content, state) {
            domManager.renderStats(content);
            domManager.renderHits(content);
            domManager.renderFacets(content, state);
            domManager.renderPagination(content);
            domManager.bindSearchObjects(state);
        }
    };

    var domManager = {

        facets: null,
        facetTemplate: null,
        facetsOrderOfDisplay: [],
        facetsLabels: [],
        hits: null,
        hitTemplate: null,
        main: null,
        pagination: null,
        paginationTemplate: null,
        markerTemplate: null,
        sliderTemplate: null,
        searchInput: null,
        searchInputIcon: null,
        sortBySelect: null,
        stats: null,
        statsTemplate: null,
        selectFavoriteGroups: [],

        bindFacets: function() {
            $(document).on('change', '.facet-select', function(e) {
                var optionSelected = $("option:selected", this);
                algoliaManager.helper.toggleRefine(optionSelected.data('facet'), optionSelected.val()).search();
            });
        },

        bindHits: function() {
        },

        bindClearAll: function() {
            $(document).on('click', '.clear-all', function(e) {
                e.preventDefault();
                $('#search-input').val('').focus();
                algoliaManager.helper.setQuery('').clearRefinements().search();
            });
        },
        bindClearFacet: function() {
            $(document).on('click', '.clear-facet', function(e) {
                e.preventDefault();
                algoliaManager.helper.clearRefinements($(this).data('facet')).search();
            });
        },

        bindPagination: function() {
            $(document).on('click', '.go-to-page', function(e) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: 0
                }, '500', 'swing');
                algoliaManager.helper.setCurrentPage(+$(this).data('page') - 1).search();
            });
        },

        bindSearch: function() {
            this.searchInput.on('keyup', function() {
                var query = $(this).val();
                domManager.toggleIconEmptyInput(query);
                algoliaManager.search();
            }).focus();
        },


        bindTripView: function() {
            $(document).on('click', '.trip_view', function(event) {
                var id = $(this).prop('id');
                id = id.substring(id.indexOf('_') + 1);

                // générer url page vue trip
            });
        },

        init: function(facetsOrderOfDisplay, facetsLabels) {
            this.searchInput = $('#search-input');
            this.searchInputIcon = $('#search-input-icon');
            this.main = $('#sl_main');
            this.sortBySelect = $('#sort-by-select');
            this.hits = $('#hits');
            this.stats = $('#stats');
            this.facets = $('#facets');
            this.pagination = $('#pagination');

            this.hitTemplate = Hogan.compile($('#hit-template').text());
            this.statsTemplate = Hogan.compile($('#stats-template').text());
            this.facetTemplate = Hogan.compile($('#facet-template').text());
            this.paginationTemplate = Hogan.compile($('#pagination-template').text());

            this.facetsOrderOfDisplay = facetsOrderOfDisplay;
            this.facetsLabels = facetsLabels;

            this.bindSearch();
            this.bindFacets();
            this.bindHits();
            this.bindPagination();
            this.bindClearAll();
            this.bindClearFacet();
            this.bindTripView();
        },

        renderFacets: function(content, state) {
            var facetsHtml = '';
            for (index in this.facetsOrderOfDisplay) {

                var facetName = this.facetsOrderOfDisplay[index];
                var facetResult = content.getFacetByName(facetName);
                var facetNameFormatted = facetName.replace(/\./g, '-');
                var facetLabel = this.facetsLabels[index];
                var facetLabel = facetLabel.replace('&#039;', "'");
                var facetContent = {};

                if (facetResult) {
                    facetContent = {
                        facet: facetName,
                        id: facetNameFormatted,
                        title: facetLabel,
                        values: content.getFacetValues(facetName, {
                            sortBy: ['name:asc', 'isRefined:desc', 'count:desc']
                        }),
                        disjunctive: false
                    };

                    facetContent.haveToDisplayDefaultOption = (facetContent.values.length !== 1);
                    facetsHtml += this.facetTemplate.render(facetContent);
                }
            }
            this.facets.html(facetsHtml);

            $('.facet-select').select2();
        },

        renderHits: function(content) {
            this.hits.html(this.hitTemplate.render(content));
        },

        renderPagination: function(content) {
            var pages = [];
            if (content.page > 3) {
                pages.push({
                    current: false,
                    number: 1
                });
                pages.push({
                    current: false,
                    number: '...',
                    disabled: true
                });
            }
            for (var p = content.page - 3; p < content.page + 3; ++p) {
                if (p < 0 || p >= content.nbPages) continue;
                pages.push({
                    current: content.page === p,
                    number: p + 1
                });
            }
            if (content.page + 3 < content.nbPages) {
                pages.push({
                    current: false,
                    number: '...',
                    disabled: true
                });
                pages.push({
                    current: false,
                    number: content.nbPages
                });
            }
            var pagination = {
                pages: pages,
                prev_page: content.page > 0 ? content.page : false,
                next_page: content.page + 1 < content.nbPages ? content.page + 2 : false
            };
            this.pagination.html(this.paginationTemplate.render(pagination));
        },

        renderStats: function(content) {
            var stats = {
                nbHits: content.nbHits,
                nbHits_plural: content.nbHits > 1,
                processingTimeMS: content.processingTimeMS,
            };

            this.stats.html(this.statsTemplate.render(stats));
        },

        toggleIconEmptyInput: function(query) {
            this.searchInputIcon.toggleClass('empty', query.trim() !== '');
        },

        bindSearchObjects: function(state) {
        }
    };

    // Init objects
    domManager.init(FACETS_ORDER_OF_DISPLAY, FACETS_LABEL);
    algoliaManager.init(APPLICATION_ID, SEARCH_ONLY_API_KEY, INDEX_NAME, PARAMS);
    algoliaManager.search();

});
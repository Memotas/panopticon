<?php
/**
 * @package   panopticon
 * @copyright Copyright (c)2023-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   https://www.gnu.org/licenses/agpl-3.0.txt GNU Affero General Public License, version 3 or later
 */

/** @var \Akeeba\Panopticon\View\Main\Html $this */

defined('AKEEBA') || die;

?>

@extends('Main/default', ['noTable' => true])

@section('main-default-sites')
    <template id="sitesListTemplate">
        <div v-if="error !== null"
             class="alert alert-danger col col-md-6 col-xl-12"
        >
            <h4 class="alert-heading">
                <span class="fa fa-fw fa-exclamation-circle" aria-hidden="true"></span>
                @lang('PANOPTICON_MAIN_DASH_ERR_CANNOTLOAD_HEAD')
            </h4>
            <p>
                @lang('PANOPTICON_MAIN_DASH_ERR_CANNOTLOAD_TEXT')
            </p>
            <details>
                <summary>@lang('PANOPTICON_SETUP_CRON_ERR_AJAX_HTTP_RAW')</summary>
                <pre class="bg-dark text-light p-2 border border-2 rounded-2">@{{ error }}</pre>
            </details>
        </div>

        <div v-if="error === null"
             class="col col-md-6 col-xl-12 mb-4 d-flex flex-row align-items-center gap-1">
            <div class="progress flex-grow-1" role="progressbar" style="height: 2em"
                 aria-label="@lang('PANOPTICON_LOGS_LBL_SR_AUTOREFRESH_TIME')"
                 :aria-valuenow="availableTime"
                 aria-valuemin="0" aria-valuemax="@{{ MAX_TIMER }}">
                <div class="progress-bar bg-secondary"
                     :style="`width: ${100*availableTime/MAX_TIMER}%`"
                >@{{ availableTime }}s</div>
            </div>
            <button type="button"
                    v-if="countdownTimer === null"
                    class="btn btn-secondary btn-sm"
                    @click="reloadData()"
            >
                <span class="fa fa-fw fa-arrow-rotate-right"></span>
                <span class="visually-hidden">@lang('PANOPTICON_MAIN_DASH_BTN_RELOAD')</span>
            </button>
            <button type="button"
                    class="btn btn-secondary btn-sm"
                    @click="toggleTimer()"
            >
                <!-- Cannot put the v-if on the icon span itself; it breaks Petite Vue. -->
                <span v-if="countdownTimer !== null">
				<span class="fa fa-fw fa-stop" aria-hidden="true"></span>
				<span class="visually-hidden">
					@lang('PANOPTICON_MAIN_DASH_BTN_TIMER_STOP')
				</span>
			</span>
                <span v-if="countdownTimer === null">
				<span class="fa fa-fw fa-play" aria-hidden="true"></span>
				<span class="visually-hidden">
					@lang('PANOPTICON_MAIN_DASH_BTN_TIMER_START')
				</span>
			</span>
            </button>
        </div>

        <div class="col" v-for="site in sites">
            <div class="card h-100">
                <a :href="site.overview_url" class="text-decoration-none">
                    <h4 class="card-header h6 fw-semibold">
                        <span class="text-muted fw-light"><small>#&thinsp;</small>@{{ site.id }}</span>&ensp;
                        <span class="text-decoration-underline link-offset-1">@{{ site.name ?? '' }}</span>
                    </h4>
                </a>
                <div class="card-body">
                    <div v-if="site.groups.length > 0">
                        <div class="card-subtitle text-end mb-2">
						<span v-for="group in site.groups" class="badge bg-secondary ms-1">
							@{{ group }}
						</span>
                        </div>
                    </div>

                    <div class="d-flex flex-row gap-2 align-items-start gap-3">
                        <div class="flex-shrink-1"
                             v-if="site.favicon"
                        >
                            <img :src="site.favicon" alt=""
                                 style="height: 3em; width: 3em; aspect-ratio: 1.0">
                        </div>
                        <div>
                            <dl style="display: grid; grid-template-columns: auto auto; grid-auto-rows: 1fr; grid-auto-flow: row; column-gap: .5em">
                                <dt>
                                    <span v-if="(site.cms ?? 'joomla') === 'joomla'">Joomla!&trade;</span>
                                    <span v-if="(site.cms ?? 'joomla') === 'wordpress'">WordPress</span>
                                </dt>
                                <dd>
								<span v-if="site.updating.cms === 1"
                                      class="text-secondary fa fa-fw fa-clock" aria-hidden="true"></span>
                                    <span v-if="site.updating.cms === 1"
                                          class="visually-hidden">
									@lang('PANOPTICON_MAIN_DASH_LBL_CMS_WILL_UPDATE')
								</span>
                                    <span v-if="site.updating.cms === 2"
                                          class="text-primary fa fa-fw fa-play" aria-hidden="true"></span>
                                    <span v-if="site.updating.cms === 2"
                                          class="visually-hidden">
                                        @lang('PANOPTICON_MAIN_DASH_LBL_CMS_UPDATING')
                                    </span>
                                    <span v-if="site.updating.cms === 3"
                                          class="text-danger fa fa-fw fa-circle-xmark" aria-hidden="true"></span>
                                    <span v-if="site.updating.cms === 3"
                                          class="visually-hidden">
                                        @lang('PANOPTICON_MAIN_DASH_LBL_CMS_UPDATE_ERROR')
                                    </span>

                                    <span v-if="!site.version" class="text-danger">@lang('PANOPTICON_TASK_JOOMLAUPDATE_LBL_UNKNOWN_VERSION')</span>
                                    <span v-else-if="site.eol" class="text-danger">
									<span class="fa fa-fw fa-book-skull" aria-hidden="true"></span>
                                        @{{ site.version }}
                                    </span>
                                    <span v-else-if="(site.latest !== null)" class="text-warning">
                                        @{{ site.version }}
                                    </span>
                                    <span v-else>@{{ site.version }}</span>
                                    <span v-if="(site.latest !== null)" class="text-secondary">
									<span class="fa fa-fw fa-arrow-right"></span>
                                        @{{ site.latest }}
                                    </span>
                                </dd>

                                <dt>@lang('PANOPTICON_MAIN_SITES_THEAD_PHP')</dt>
                                <dd>@{{ site.php }}</dd>

                                <dt v-if="(site.overrides > 0)">
                                    @lang('PANOPTICON_OVERRIDES_TITLE')
                                </dt>
                                <dd v-if="(site.overrides > 0)">
								<span class="badge bg-warning">
									<span class="fa fa-fw fa-arrows-to-circle" aria-hidden="true"></span>
									@{{ site.overrides }}
								</span>
                                </dd>

                                <dt v-if="(site.extensions > 0) && (site.cms ?? 'joomla') === 'joomla'">
                                    @lang('PANOPTICON_MAIN_DASH_LBL_EXTENSIONS')
                                </dt>
                                <dt v-if="(site.extensions > 0) && (site.cms ?? 'joomla') === 'wordpress'">
                                    @lang('PANOPTICON_MAIN_DASH_LBL_EXTENSIONS_WORDPRESS')
                                </dt>
                                <dd v-if="(site.extensions > 0)">
								<span v-if="site.updating.extensions === 1"
                                      class="text-secondary fa fa-fw fa-clock" aria-hidden="true"></span>
                                    <span v-if="site.updating.extensions === 1"
                                          class="visually-hidden">
									<span v-if="(site.cms ?? 'joomla') === 'joomla'">@lang('PANOPTICON_MAIN_DASH_LBL_EXTENSIONS_WILL_UPDATE')</span>
									<span v-if="(site.cms ?? 'joomla') === 'wordpress'">@lang('PANOPTICON_MAIN_DASH_LBL_EXTENSIONS_WP_WILL_UPDATE')</span>
								</span>
                                    <span v-if="site.updating.extensions === 2"
                                          class="text-primary fa fa-fw fa-play" aria-hidden="true"></span>
                                    <span v-if="site.updating.extensions === 2"
                                          class="visually-hidden">
									<span v-if="(site.cms ?? 'joomla') === 'joomla'">@lang('PANOPTICON_MAIN_DASH_LBL_EXTENSIONS_UPDATING')</span>
									<span v-if="(site.cms ?? 'joomla') === 'wordpress'">@lang('PANOPTICON_MAIN_DASH_LBL_EXTENSIONS_WP_UPDATING')</span>
								</span>
                                    <span v-if="site.updating.extensions === 3"
                                          class="text-danger fa fa-fw fa-circle-xmark" aria-hidden="true"></span>
                                    <span v-if="site.updating.extensions === 3"
                                          class="visually-hidden">
									<span v-if="(site.cms ?? 'joomla') === 'joomla'">@lang('PANOPTICON_MAIN_DASH_LBL_EXTENSIONS_UPDATE_ERROR')</span>
									<span v-if="(site.cms ?? 'joomla') === 'wordpress'">@lang('PANOPTICON_MAIN_DASH_LBL_EXTENSIONS_WP_UPDATE_ERROR')</span>
								</span>

                                    <span class="badge bg-warning">
									<span class="fa fa-fw fa-box-open" aria-hidden="true"></span>
									@{{ site.extensions }}
								</span>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex flex-row justify-content-between">
                    <div class="text-secondary">
                        <!-- CMS icon -->
                        <span v-if="(site.cms ?? 'joomla') === 'joomla'" class="fab fa-fw fa-joomla" aria-hidden="true"></span>
                        <span v-if="(site.cms ?? 'joomla') === 'wordpress'" class="fab fa-fw fa-wordpress" aria-hidden="true"></span>

                        <!-- Errors collecting site information -->
                        <span v-if="(site.errors.site !== null)"
                              class="text-warning"
                        >
						<span class="fa fa-fw fa-triangle-exclamation" aria-hidden="true"></span>
						<span class="visually-hidden">
							@lang('PANOPTICON_MAIN_DASH_ERR_CMS')
						</span>
					</span>

                        <!-- Errors collecting extensions information -->
                        <span v-if="(site.errors.site !== null)"
                              class="text-danger"
                        >
						<span class="fa fa-fw fa-circle-exclamation" aria-hidden="true"></span>
						<span v-if="(site.cms ?? 'joomla') === 'joomla'"
                              class="visually-hidden">
                            @lang('PANOPTICON_MAIN_DASH_ERR_EXT')
						</span>
						<span v-if="(site.cms ?? 'wordpress') === 'wordpress'"
                              class="visually-hidden">
                            @lang('PANOPTICON_MAIN_DASH_ERR_EXT_WP')
						</span>
					</span>
                    </div>

                    <div>
                        <a href="@{{ site.url }}" target="_blank"
                           class="link-opacity-50 link-opacity-100-hover link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">
                            @{{ site.url }}
                            <span class="fa fa-external-link-alt fa-xs text-muted" aria-hidden="true"></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <div class="container" id="sitesList">
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-2 gy-3"
             v-scope="SitesList({})" @vue:mounted="mounted">
        </div>
    </div>
@endsection
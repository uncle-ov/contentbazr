@extends('layouts.app')
@section('content')
<style>
    section img { max-width: 100%; }

/*DAWIH*/
/*--------------------------------------------------------------
# Hero Section
--------------------------------------------------------------*/
.hero {
  background: url(https://www.akpachiogu.xyz/public/img/cover.jpg) top center
    no-repeat;
  background-size: cover;
  overflow-x: hidden;
}
.hero h1 {
  margin: 0;
  font-size: 48px;
  font-weight: 700;
  color: #012970;
}
.hero h2 {
  color: #444444;
  margin: 15px 0 0 0;
  font-size: 26px;
}

.hero .btn-get-started {
  margin-top: 30px;
  line-height: 0;
  padding: 15px 40px;
  border-radius: 4px;
  transition: 0.5s;
  color: #fff;
  background: #4154f1;
  box-shadow: 0px 5px 30px rgba(65, 84, 241, 0.4);
}
.hero .btn-get-started span {
  font-family: "Nunito", sans-serif;
  font-weight: 600;
  font-size: 16px;
  letter-spacing: 1px;
}
.hero .btn-get-started i {
  margin-left: 5px;
  font-size: 18px;
  transition: 0.3s;
}
.hero .btn-get-started:hover i {
  transform: translateX(5px);
}
.hero .hero-img {
  text-align: right;
}
.font-weight-bold.circle.text-white.rounded-circle.d-flex.align-items-center.justify-content-center.position-relative.border.border-white.mt-4 {
  width: 60px;
  height: 60px;
  left: 300px;
  bottom: 40px;
}
@media (min-width: 1024px) {
  .hero {
    background-attachment: fixed;
  }
}
@media (max-width: 991px) {
  .hero {
    height: auto;
    padding: 120px 0 60px 0;
  }
  .hero .hero-img {
    text-align: center;
    margin-top: 80px;
  }
  .hero .hero-img img {
    width: 80%;
  }
}
@media (max-width: 768px) {
    .margt-15-mobile {
        margin-top: 15px;
    }

  .hero {
    text-align: center;
  }
  .hero h6 {
    font-size: 17px;
    left: 20px !important;
  }

  .hero .hero-img img {
    width: 100%;
  }

  hr.my-5 {
    width: 100% !important;
  }
  .row.tm-content-box {
    margin-left: 10px;
  }
}
.testi_oval {
    position: relative;
    padding: 30px;
    background-color: #dae3f3 !important;
    border-radius: 30px;
    padding-left: 15px;
    margin: 0 auto;
}.testi_oval .content_wrap {
    border-left: 10px solid rgba(0,0,0,.8);padding-left: 15px;
}.testi_oval img.rounded-circle {
    position: absolute;
    right:10px;
    bottom:-50px;
    width: 100px;
    height: 100px;
}.testi_oval small {
    font-size: 80%;
    opacity:.7;
}

.bg-light {
    background: #ecf1f8 !important;
}

.fancy_number {
    padding-left: 120px;
    background-repeat: no-repeat;
    background-position: 0px top;
    background-size: auto 200px;
}
.fancy_number.one { background-image: url({{ URL('public/images/home/one.png?x') }}); }
.fancy_number.two { background-image: url({{ URL('public/images/home/two.png?x') }}); }
.fancy_number.three { background-image: url({{ URL('public/images/home/three.png?x') }}); }
.fancy_number.four { background-image: url({{ URL('public/images/home/4.png?x') }}); }
@media(max-width: 540px) {
    .fancy_number {
        padding-left: 60px;
        background-size: auto 100px;
    }
}

.btn-main {
    background: #12142E;
    color: #fff;
    border-radius: 0 !important;
    min-width: 200px;
    text-align: left;
    padding-left: 30px !important;
    padding-right: 30px !important;
}.btn-main::after {
    color: #D9182D;
    float: right;
}
</style>
<div class="container-fluid home-cover" style="padding-bottom: 100px;">
    <div class="mb-4 position-relative custom-pt-6">
        <div class="container px-5">
            <div class="row">
                <div class="col-md-5 col-lg-4 d-flex flex-column justify-content-center">
                    <img src="{{url('/public/images/home/logo-design.png')}}" alt="" style="max-width: 300px;" />
                    <div data-aos="fade-up" data-aos-delay="600">
                        <div class="text-lg-start">
                            <h6 class=""
                                style="
                                color: white;
                                font-weight: 200 !important;
                                opacity: .6;
                                padding: 30px 0;
                                "
                                >EASILY CREATE A WIDE ARRAY OF HIGH-RESOLUTION VISUAL CONTENT FOR FREE USING JUST ONE TOOL - THE MICROSOFT POWERPOINT.
                            </h6>
                            <form
                                action="{{url('/search')}}"
                                method="get"
                                class="position-relative"
                                >
                                <i class="bi bi-search btn-search"></i>
                                <input
                                    class="form-control form-control-lg ps-5 input-search-lg border-0 search-lg"
                                    type="text"
                                    name="q"
                                    autocomplete="off"
                                    placeholder="Search"
                                    required
                                    minlength="3"
                                    />
                                <div style="padding: 10px;"></div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-1"></div>
                <div
                    class="col-lg-7 col-md-6 hero-img"
                    data-aos="zoom-out"
                    data-aos-delay="200"
                    >
                    <img src="{{url('/public/images/home/template-collage.png')}}" class="img-fluid" alt="" />
                </div>
            </div>
        </div>
    </div>
</div>
<!-- container-fluid -->


<section id="whatwedo" class="section py-5">
    <div class="container">
        <div class="text-center" style="max-width:800px;margin:0 auto;">
            <h2 class="tm-text-primary mb-4 tm-section-title" style="text-align: center; margin-top:50px; margin-bottom: 30px !important ">Grow Your Influence With Content.</h2>
            <p><p>With a database of over <strong>250K templates</strong> and counting in more than <strong>60 formats, ContentBazr&reg;</strong> enables you to leverage your existing <strong>Microsoft PowerPoint*</strong> tool to create stunning multimedia content that produce desired results. <strong>No other software required! No design skills needed!</strong></p></p>
            
            <div style="padding-top: 30px;max-width: 560px;margin: 0 auto;">
                <div class="embed-responsive embed-responsive-16by9" style="border-radius: 30px;border:10px solid #fff;box-shadow: 0px 20px 50px rgba(0,0,0,.2)">
                  <iframe class="embed-responsive-item" src="{{ Helper::convertYouTubeToEmbed($settings->homepage_video) }}"></iframe>
                </div>
            </div>
            <div style="padding: 15px;"></div>
            
            <div class="row">
                <div class="col-sm-6 margt-15-mobile">
                    <img alt="microsoft" src="{{ URL('public/images/home/microsoft1.png') }}" style="max-width: 700px; width: 100%;" />
                </div>
                <div class="col-sm-6 margt-15-mobile">
                    <img alt="microsoft" src="{{ URL('public/images/home/microsoft2.png') }}" style="max-width: 700px; width: 100%;" />
                </div>
            </div>
        </div>
    </div>
</section>


<section id="whatwedo" class="section py-5 bg-light">
    <div class="container">
        <div class="row tm-content-box">
            <!-- first row -->
            <div class="col-lg-12 col-xl-12">
                <div class="tm-intro-text-container">
                    <h2 class="tm-text-primary mb-4 tm-section-title" style="text-align: center; margin-top:50px; margin-bottom: 50px !important ">Create Content in 4 Easy Steps</h2>
                    <p class="mb-4 tm-intro-text">
                    </p>
                </div>
            </div>
        </div>
        <!-- first row -->
        <div class="row tm-content-box">
            <!-- second row -->
            <div class="col-md-6 fancy_number one">
                <div class="tm-intro-text-container position-relative">
                    <h4 class="tm-text-primary mb-4">Choose a design template</h4>
                    <p class="mb-4 tm-intro-text">
                        Our database contains thousands of beautifully designed and categorized templates.  You may search templates by using keywords related to industry, format type, use case or even colour.  Download your choice and open in Powerpoint for editing.
                    </p>
                    <img src="{{url('/public/images/home/01_select_template.png')}}" alt="" style="margin-bottom: 40px; width: 300px;"/>
                </div>
            </div>
            <div class="col-md-6 fancy_number two">
                <div class="tm-intro-text-container position-relative">
                    <h4 class="tm-text-primary mb-4">Customize the design</h4>
                    <p class="mb-4 tm-intro-text">
                        You can edit any template until you’ve created a masterpiece by changing up the copy, font, photos, colours and logo elements right in PowerPoint.  The ease of use of PowerPoint makes our templates easy to keep on brand.
                    </p>
                    <img src="{{url('/public/images/home/02_edit_template.png')}}" alt="" style="margin-bottom: 40px; width: 300px;"/>
                </div>
            </div>
        </div>
        <!-- second row -->
        <div class="row tm-content-box  py-5">
            <!-- third row -->
            <div class="col-md-6 fancy_number three">
                <div class="tm-intro-text-container position-relative" style="height: 100%;">
                    <h4 class="tm-text-primary mb-4">Export the design</h4>
                    <p class="mb-4 tm-intro-text">
                        When you are happy with your design you may now export or save it in the preferred format.PowerPoint offers so many format types. Simply use the “Save As” or “Export” function in PowerPoint. You can then share your content to any platform of your choosing without limits.
                    </p>
                    <img src="{{url('/public/images/home/export_template.png')}}" alt="" style="margin-bottom: 40px; width: 300px;"/>
                </div>
            </div>
            <div class="col-md-6 fancy_number four">
                <div class="tm-intro-text-container position-relative" style="height: 100%;">
                    <h4 class="tm-text-primary mb-4">Post & delight your audience!</h4>
                    <p class="mb-4 tm-intro-text">
                        The wide spectrum of content formats available means that you can reach your audience  wherever they are and however they wish to consume your content. Grab your audience’s  attention and get the views, clicks, shares and sales you want.
                    </p>
                    <img src="{{url('/public/images/home/delight_audience.png')}}" alt="" style="margin-bottom: 40px; width: 300px;"/>
                </div>
            </div>
        </div>
        <!-- third row -->
    </div>
</section>
<div class="btn-block text-center  py-5 Content Design Features">
    <h3
        class="m-0"style="margin-bottom: 20px !important;margin-top: 20px !important;color: black;font-weight: bold;">Content Design Features
    </h3>
    <p style="padding:  0 10%;max-width: 1000px;margin: 0 auto;">
        ContentBazr provides more than enough design assets, templates and fonts you need to create amazing
        designs all for free. Our FREE plan features are priced as PRO features in tools like Canva. Own your designs
        - design and export high-resolution multimedia content without the strictures of the paid design tools and
        ecosystems. The Pro features they tout and make you pay for are indeed standard PowerPoint features. You 
        just didn’t know it but that changes now. Here’s to creative freedom!
    </p>
</div>

<section id="whatwedo" class="tm-section-pad-top">
    <div class="container"style="max-width: 900px;">
        <div class="col-md-12">
            <div class="con-text">
                <h5 style="position: relative;">Tons of Free Premium Content</h5>
                <hr class="my-5" style="width: 800px;position: relative;color: red;margin-top: 0px !important;opacity: 25 !important; margin-bottom: 30px !important">
            </div>
        </div>
        <div class="row tm-content-box">
            <!-- second row -->
            <div class="col-lg-1">
                <img alt="asset" src="{{ URL('public/images/home/icons/assets.png') }}" width="80">
            </div>
            <div class="col-lg-5">
                <div class="tm-intro-text-container">
                    <h6 class="tm-text-primary ">Premium Assets</h6>
                    <p class="mb-4 tm-intro-text">
                        Link to millions of free photos, vectors, native  and third-party graphic design assets 100%  free for commercial use.
                    </p>
                </div>
            </div>
            <div class="col-lg-1">
                <img alt="asset" src="{{ URL('public/images/home/icons/typography.png') }}" width="80">
            </div>
            <div class="col-lg-5">
                <div class="tm-intro-text-container">
                    <h6 class="tm-text-primary">Amazing Typography</h6>
                    <p class="mb-4 tm-intro-text">Link to several native and free fonts sources. All  templates come with fonts embedded. No need to download them.
                    </p>
                </div>
            </div>
        </div>
        <!-- second row -->
        <div class="row tm-content-box">
            <!-- third row -->
            <div class="col-lg-1">
                <img alt="asset" src="{{ URL('public/images/home/icons/templates.png') }}" width="80">
            </div>
            <div class="col-lg-5">
                <div class="tm-intro-text-container">
                    <h6 class="tm-text-primary">Premium Templates</h6>
                    <p class="mb-4 tm-intro-text">
                        Thousands of native templates in Office plus a growing library of  curated premium ones from ContentBazr. 
                    </p>
                </div>
            </div>
            <div class="col-lg-1">
                <img alt="asset" src="{{ URL('public/images/home/icons/canvas.png') }}" width="80">
            </div>
            <div class="col-lg-5">
                <div class="tm-intro-text-container">
                    <h6 class="tm-text-primary">Powerful Canvas</h6>
                    <p class="mb-4 tm-intro-text">
                        Dynamic canvas means you can import and edit media of all types and export in several popular multimedia formats. 
                    </p>
                </div>
            </div>
        </div>
        <!-- third row -->
    </div>
</section>
<section id="whatwedo" class="tm-section-pad-top">
    <div class="container"style="max-width: 900px;">
        <div class="col-md-12">
            <div class="con-text">
                <h5 style="position: relative;margin-top:20px;">Powerful Assets Organization</h5>
                <hr class="my-5" style="width: 800px; position: relative;color: red;margin-top: 0px !important;opacity: 25 !important; margin-bottom: 30px !important">
            </div>
        </div>

        <div class="row tm-content-box">
            <!-- second row -->
            <div class="col-lg-1">
                <img alt="asset" src="{{ URL('public/images/home/icons/branding.png') }}" width="80">
            </div>
            <div class="col-lg-5">
                <div class="tm-intro-text-container">
                    <h6 class="tm-text-primary">Stay On-Brand</h6>
                    <p class="mb-4 tm-intro-text">Build a brand kit for the whole team. Create folders and curate brand assets keeping it all for the team in one place.</p>
                </div>
            </div>
            <div class="col-lg-1">
                <img alt="asset" src="{{ URL('public/images/home/icons/cloud_storage.png') }}" width="80">
            </div>
            <div class="col-lg-5">
                <div class="tm-intro-text-container">
                    <h6 class="tm-text-primary">Massive Cloud Storage</h6>
                    <p class="mb-4 tm-intro-text">1TB for Office subscription and free 5GB for non- subscribers on OneDrive. 1TB? Yes! That’s more than you could ever need!
                    </p>
                </div>
            </div>
        </div>
        <!-- second row -->
        <div class="row tm-content-box">
            <!-- third row -->
            <div class="col-lg-1">
                <img alt="asset" src="{{ URL('public/images/home/icons/smart_folder.png') }}" width="80">
            </div>
            <div class="col-lg-5">
                <div class="tm-intro-text-container">
                    <h6 class="tm-text-primary">Smart Folders & Search</h6>
                    <p class="mb-4 tm-intro-text">
                        Easily search and find organized assets with tags, and keywords searches within  folders/files offline and OneDrive.from ContentBazr. 
                    </p>
                </div>
            </div>
            <div class="col-lg-1">
                <img alt="asset" src="{{ URL('public/images/home/icons/team_collab.png') }}" width="80">
            </div>
            <div class="col-lg-5">
                <div class="tm-intro-text-container">
                    <h6 class="tm-text-primary">Team Collaboration</h6>
                    <p class="mb-4 tm-intro-text">
                        Collaborate with your team online and easily create awesome content together. Hold control over who can view and edit designs.
                    </p>
                </div>
            </div>
        </div>
        <!-- third row -->
    </div>
    <div class="container"style=" max-width: 900px;">
        <div class="col-md-12">
            <div class="con-text">
                <h5 style="margin-top:20px;position: relative;">Simple No-Leash Creativity</h5>
                <hr class="my-5" style="width: 800px;position: relative;color: red;margin-top: 0px !important;opacity: 25 !important; margin-bottom: 30px !important">
            </div>
        </div>
        <div class="row tm-content-box">
            <!-- second row -->
            <div class="col-lg-1">
                <img alt="asset" src="{{ URL('public/images/home/icons/publishing.png') }}" width="80">
            </div>
            <div class="col-lg-5">
                <div class="tm-intro-text-container">
                    <h6 class="tm-text-primary">Powerful Publishing</h6>
                    <p class="mb-4 tm-intro-text">Publish pixel perfect content/sizes for multiple formats and content channels. Templates are perfectly sized for use.</p>
                </div>
            </div>
            <div class="col-lg-1">
                <img alt="asset" src="{{ URL('public/images/home/icons/repurpose_content.png') }}" width="80">
            </div>
            <div class="col-lg-5">
                <div class="tm-intro-text-container">
                    <h6 class="tm-text-primary ">Repurpose Content</h6>
                    <p class="mb-4 tm-intro-text">Easily repurpose content e.g. Instagram Carousel to eBook with no hassles, complex conversion  tools and needless editing.</p>
                </div>
            </div>
        </div>
        <!-- second row -->
        <div class="row tm-content-box">
            <!-- third row -->
            <div class="col-lg-1">
                <img alt="asset" src="{{ URL('public/images/home/icons/story_boarding.png') }}" width="80">
            </div>
            <div class="col-lg-5">
                <div class="tm-intro-text-container">
                    <h6 class="tm-text-primary">Storyboarding Features</h6>
                    <p class="mb-4 tm-intro-text">Easily add animations, tours, actions, transitions, interactivity and triggers. Tell  your stories in more than one way. </p>
                </div>
            </div>
            <div class="col-lg-1">
                <img alt="asset" src="{{ URL('public/images/home/icons/office_suite.png') }}" width="80">
            </div>
            <div class="col-lg-5">
                <div class="tm-intro-text-container">
                    <h6 class="tm-text-primary">Office Suite Integration</h6>
                    <p class="mb-4 tm-intro-text">
                        Work seamlessly across all Office suite apps with no formatting constraints. Stay with the trusted tools that have worked for ages. 
                    </p>
                </div>
            </div>
        </div>
        <!-- third row -->
    </div>
</section>

<section class="section py-5 bg-light">
    <div class="container">
        @if ($images->total() != 0)
        <div class="btn-block text-center mb-5">
            @if ($settings->show_images_index == 'latest')
            <h3 class="m-0">{{trans('misc.recent_photos')}}</h3>
            <p>
                {{ trans('misc.latest_desc') }}
            </p>
            @endif
            @if ($settings->show_images_index == 'featured')
            <h3 class="m-0">{{trans('misc.featured_photos')}}</h3>
            <p>
                {{ trans('misc.featured_desc') }}
            </p>
            @endif
        </div>
        @include('includes.images')
        <div class="w-100 d-block text-center mt-5">
            <a href="{{ $settings->show_images_index == 'latest' ? url('latest') : url('featured') }}" class="btn btn-lg btn-main rounded-pill btn-outline-custom px-4 arrow px-5">
            {{ trans('misc.view_all') }}
            </a>
        </div>
        @else
        <h4 class="text-center">
            <div class="d-block w-100 display-2">
                <i class="bi bi-images"></i>
            </div>
            {{ trans('misc.no_images_published') }}
        </h4>
        <div class="w-100 d-block text-center mt-3">
            <a href="{{ url('upload') }}" class="btn btn-lg btn-main rounded-pill btn-outline-custom px-4 arrow px-5">
            {{ trans('users.upload') }}
            </a>
        </div>
        @endif
        @if ($settings->google_adsense && $settings->google_ads_index == 'on' && $settings->google_adsense_index != '')
        <div class="col-md-12 mt-3">
            {!! $settings->google_adsense_index !!}
        </div>
        @endif
    </div>
</section>
<!-- container photos -->
<!-- {{--
    @if ($images->total() != 0)
    <section class="section py-5 py-large bg-light">
      <div class="container">
        <div class="row align-items-center">
        <div class="col-12 col-lg-7 text-center mb-3 px-5">
          <img src="{{ url('public/img', $settings->img_section) }}" class="img-fluid">
        </div>
        <div class="col-12 col-lg-5 text-lg-start text-center">
          <h1 class="m-0 card-profile">{{ trans('misc.title_section_home') }}</h1>
          <div class="col-12 p-0">
            <p class="py-4 m-0 text-muted">{{ trans('misc.desc_section_home') }}</p>
          </div>
          <a href="{{ url('latest') }}" class="btn btn-lg btn-main rounded-pill btn-outline-custom  px-4 arrow">
            {{ trans('misc.explore') }}
          </a>
        </div>
      </div>
      </div>
    </section>
    @endif
    
    
    @if ($settings->show_counter == 'on')
    <section class="section py-2 bg-dark text-white">
      <div class="container">
        <div class="row">
          <div class="col-md-4">
            <div class="d-flex py-3 my-1 my-lg-0 justify-content-center">
              <span class="me-3 display-4"><i class="bi bi-people align-baseline"></i></span>
              <div>
                <h3 class="mb-0"><span class="counter">{{ User::whereStatus('active')->count() }}</span></h3>
                <h5>{{trans('misc.members')}}</h5>
              </div>
            </div>
    
          </div>
          <div class="col-md-4">
            <div class="d-flex py-3 my-1 my-lg-0 justify-content-center">
              <span class="me-3 display-4"><i class="bi bi-download align-baseline"></i></span>
              <div>
                <h3 class="mb-0"><span class="counter">{{ Downloads::count() }}</span></h3>
                <h5 class="font-weight-light">{{trans('misc.downloads')}}</h5>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="d-flex py-3 my-1 my-lg-0 justify-content-center">
              <span class="me-3 display-4"><i class="bi bi-images align-baseline"></i></span>
              <div>
                <h3 class="mb-0"> <span class="counterStats">{{ Images::whereStatus('active')->count() }}</span></h3>
                <h5 class="font-weight-light">{{trans('misc.stock_photos')}}</h5>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    @endif
    --}}-->

    @if ($settings->show_counter == 'on')
    <section class="section py-2 bg-dark text-white">
      <div class="container">
        <div class="row">
          <div class="col-md-4">
            <div class="d-flex py-3 my-1 my-lg-0 justify-content-center">
              <span class="me-3 display-4"><i class="bi bi-people align-baseline"></i></span>
              <div>
                <h3 class="mb-0"><span class="counter">{{ User::whereStatus('active')->count() }}</span></h3>
                <h5>{{trans('misc.members')}}</h5>
              </div>
            </div>

          </div>
          <div class="col-md-4">
            <div class="d-flex py-3 my-1 my-lg-0 justify-content-center">
              <span class="me-3 display-4"><i class="bi bi-download align-baseline"></i></span>
              <div>
                <h3 class="mb-0"><span class="counter">{{ Downloads::count() }}</span></h3>
                <h5 class="font-weight-light">{{trans('misc.downloads')}}</h5>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="d-flex py-3 my-1 my-lg-0 justify-content-center">
              <span class="me-3 display-4"><i class="bi bi-images align-baseline"></i></span>
              <div>
                <h3 class="mb-0"> <span class="counterStats">{{ Images::whereStatus('active')->count() }}</span></h3>
                <h5 class="font-weight-light">{{trans('misc.stock_photos')}}</h5>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    @endif

    <section class="section  py-5 bg-light">
      <div class="container">
        <div class="row align-items-center">
        <div class="col-12 col-lg-7 text-center mb-3 px-5">
          <img src="{{ url('public/images/home/05_download_assets.png') }}" class="img-fluid">
        </div>
        <div class="col-12 col-lg-5 text-lg-start text-center pb-5">
          <h1 class="m-0 card-profile">Design Resources Library</h1>
          <div class="col-12 p-0">
            <p class="py-4 m-0 text-muted">Explore our top-quality featured resources, chosen by our team</p>
          </div>
          <a href="{{ url('category/industry') }}" class="btn btn-lg btn-main rounded-pill btn-outline-custom  px-4 arrow">
            View all
          </a>
        </div>
      </div>
      </div>
    </section>

<section class="about bg-light">
    <div class="container">
        <div class="about-info" style="text-align: center;">
            <h2 class="mb-4 " >What Our Users are Saying</h2>
        </div>
        <div class="row" style="max-width: 800px;margin: 0 auto;padding-bottom: 50px;">
            <div class="col-sm-6">
                <img alt="microsoft" src="{{ URL('public/images/home/testimonial_01.png') }}" style="max-width: 700px; width: 100%;" />
            </div>
            <div class="col-sm-6">
                <img alt="microsoft" src="{{ URL('public/images/home/testimonial_02.png') }}" style="max-width: 700px; width: 100%;" />
            </div>
        </div>
        
    </div>
</section>
    
<section class="section py-5 py-large">
    <div class="container">
        <div class="about-info" style="text-align: center;">
            <h2 class="mb-4 " >You’re in good company!</h2>
        </div>
        <div class="row" style="max-width: 1000px;margin: 0 auto;">
            <div class="col-md-12 text-center" style="max-width: 800px;margin: 0 auto;">
                <p>Microsoft needs little introduction as the No. 1 software company in the world by value. Over 90% of the Fortune 500 companies now use the Microsoft suite of software.</p>
                <p>With ContentBazr® you share a common language with teams within and outside your organisation with a tool everyone (including your clients and contractors) is familiar with. You scale visual communications and create an open culture with a single source of truth for shared libraries, styles and reusable content.</p>
                <p>Whenever there’s a shared language and understanding, success naturally follows!</p>
            </div>
            <div class="col-sm-6"><img class="mt-3" src="{{URL('public/images/home/home-logos_01.svg')}}" alt="partners"></div>
            <div class="col-sm-6"><img class="mt-3" src="{{URL('public/images/home/home-logos_02.svg')}}" alt="partners"></div>
        </div>
        
    </div>
</section>
@if ($settings->show_categories_index == 'on')
<section class="section py-5 py-large bg-light">
    <div class="container">
    <div class="btn-block text-center mb-5">
        <h3 class="m-0">{{trans('misc.categories')}}</h3>
        <p>
            {{trans('misc.browse_by_category')}}
        </p>
    </div>
    <div class="row">
    @include('includes.categories-listing')
    @if ($categories->total() > 4)
    <div class="w-100 d-block text-center mt-4">
        <a href="{{ url('categories') }}" class="btn btn-lg btn-main rounded-pill btn-outline-custom px-4 arrow px-5">
        {{ trans('misc.view_all') }}
        </a>
    </div>
    @endif
</section>
@endif
@endsection
@section('javascript')
<script type="text/javascript">
    $('#imagesFlex').flexImages({ rowHeight: 320, maxRows: 8, truncate: true });
    
    @if (session('success_verify'))
    swal({
        title: "{{ trans('misc.welcome') }}",
        text: "{{ trans('users.account_validated') }}",
        type: "success",
        confirmButtonText: "{{ trans('users.ok') }}"
        });
    @endif
    
    @if (session('error_verify'))
    swal({
        title: "{{ trans('misc.error_oops') }}",
        text: "{{ trans('users.code_not_valid') }}",
        type: "error",
        confirmButtonText: "{{ trans('users.ok') }}"
        });
    @endif
    
</script>
@endsection
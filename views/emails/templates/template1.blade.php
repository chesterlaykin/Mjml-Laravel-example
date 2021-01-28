@php
    $testing = false;
    $header = 'https://images.unsplash.com/photo-1574800048190-2839bc9ff666?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=60';
 
     //print_r($newsletter->header); die();
    if(!$testing){ 
        $header = (isset($newsletter->header) && is_array($newsletter->header)) ? url('/storage/' . $newsletter->header['medium_path'])  : null;
    }
    $color_schemes = collect($newsletter->template->properties['color_schemes']);
    $palette = (object) $color_schemes->where('name', $color_scheme)->first()['palette'];

@endphp

<mjml>    
    <mj-head>
        <mj-attributes>
            <mj-wrapper padding="0px"></mj-wrapper>
            {{-- <mj-column padding="0"></mj-column> --}}
            <mj-font name="Oswald" href="https://fonts.googleapis.com/css?family=Oswald" />
            <mj-text font-family="Oswald, Arial, Helvetica, sans-serif"
                     padding="15px"
                     color="#101010"
                     font-size="15px"
                line-height="23px">
            </mj-text>
            <mj-section padding="0"></mj-section>
            <mj-class name="section_bg" /> 
            <mj-button text-transform="uppercase" inner-padding="15px" background-color="{{ $palette->btn_bg_color }}"
                       font-weight="bold">
            </mj-button>
            <mj-image border-radius="6px"></mj-image>
            <mj-divider border-color="transparent" 
                      padding-left="25px" padding-right="25px" border-width="1px"
                padding-top="0" padding-bottom="0">
            </mj-divider>
        </mj-attributes>

        <mj-style inline="inline">
          a { text-decoration: none!important; color: #ff872f !important; } 
          td > a{color:white !important; display:block !important; } 
            @if ( isset($newsletter->header) && is_array($newsletter->header))
            .mcbi-header_1 {/* @editable */background-image:{{ $header }} !important;/* @editable */background-position:center !important;/* @editable */background-repeat:no-repeat !important; background-size:cover !important;}
            @endif
            
            {{-- start template styles --}}
            
            .bg { background-color: {{ $palette->bg_color }}; }
            .btn-bg-color { background-color: {{ $palette->btn_bg_color }}; }
            .btn-text-color { color: {{ $palette->btn_text_color }}; }
            .btn { background-color: {{ $palette->btn_bg_color }}; color: {{ $palette->btn_text_color }}; }
            .wrap-section-bg:nth-child(odd){
                background-color:#f7f7f7;
            }
            .header-text{
                padding-bottom:30px;
            }
            .section{
                 
            }
            {{-- end template styles --}}
            
        </mj-style>
        <mj-style>
            @media (max-width:600px) {
              .marginauto-mobile table{
                  margin:auto !important;  
              }
              .aligncenter-mobile > div {
                  text-align:center !important;
              }
            }
        </mj-style>
       
        {{-- table table table table{width:100% !important; --}}
        
    </mj-head>
    <mj-body>
      
        
         
          {{-- Company Header --}}
        
        {{-- <mj-section  padding="6px 0" padding-bottom="0" background-color="#f0f0f0">

            <mj-column>
                <mj-text  
                      color="#101010"
                      css-class="aligncenter-mobile"
                >
                    @if ($mailinglist && is_object($mailinglist->association))
                   
                       <b>{{ $mailinglist->association->name }} </b> - {{ $mailinglist->name }} 
                    @elseif($mailinglist)
                        {{ $mailinglist->name }} 
                    @else
                       (Listans namn)
                    @endif 
                </mj-text> 
            </mj-column>
            <mj-column>
                <mj-text 
                      font-size="15px" 
                        align="right"
                      css-class="aligncenter-mobile"
                > 
                         
                </mj-text> 
            </mj-column>
        </mj-section>  --}}
       

        

        
        {{-- Image Header --}}
        
        <mj-section>
            <mj-column>
                @if ($newsletter->title && !$title_positioned_on_header_image)
                    <mj-text align="center" 
                        color="#232323" 
                        font-size="27px"  
                        line-height="38px"
                        css-class="header-text" 
                    > 
                    {{ $newsletter->title }}
                
                    </mj-text>  
                @endif   
            </mj-column>    
        </mj-section>            
        <mj-section background-url="{{ $header }}" 
                    background-size="cover"
                    background-repeat="no-repeat"
                    padding-top="140px"
                    padding-bottom="140px"
        >
        
            <mj-column> 
                @if ($newsletter->title && $title_positioned_on_header_image)
                    <mj-text align="center" 
                            color="#232323" 
                            font-size="27px" 
                            line-height="38px"
                            css-class="header-text"
                    > 
                        {{ $newsletter->title }}
                    
                    </mj-text> 
                @endif
            </mj-column>
        </mj-section>
            {{--
            <mj-section>
                <mj-column>
                        <mj-image width="165px" src="{{  url( $header )   }}" > </mj-image> 
                </mj-column>   
            </mj-section>
             --}}
        

          
        {{-- Boxes (1-3) (placement 1) --}}
       
        @if (isset($boxes['placement_1']) && count($boxes['placement_1']))
            @foreach ($boxes['placement_1'] as $box)
        <mj-wrapper css-class="wrap-section-bg">    {{-- #f3f3f3  --}} 
                <mj-section padding-top="18px"> 
                    
                    <mj-column>
                        <mj-text font-size="22px" line-height="29px">
                            <b>{{ $box['title'] }}</b>
                        </mj-text>
                    </mj-column> 
                </mj-section> 
                <mj-section> 
                    <mj-column >
                      <mj-text> {!! $box['content'] !!} </mj-text>
                    </mj-column> 
                    @isset ($box['image']) 
                        <mj-column >
                            <mj-image src="{{ 
             url( 'storage/newsletters/boxes/sizes/' . getFilenameWithoutExt($box['image']) . '-medium' . getExtension($box['image'])  ) 
             }}" > 
                            </mj-image> 
                        </mj-column> 
                        {{-- padding="0"  --}}
                    @endisset
                </mj-section>
         
            {{-- button link --}} 
                @isset ($box['link']) 
                    <mj-section padding="0 0 0 0"> 
                        <mj-column>

                           <mj-button href="{{ $box['link'] }}" align="left">{{ $box['link_title'] }}</mj-button>
                        </mj-column> 
                    </mj-section> 
                 
                    {{--   --}}
                @endisset
                 
                 
        </mj-wrapper>   
            
            @endforeach
        @endif

       
         {{-- Box (1) (placement 2) section with image to right --}}
        
        @if (isset($boxes['placement_2']) && count($boxes['placement_2']))

        <mj-wrapper css-class="section">   {{--  background-color="white" --}}    

            @foreach ($boxes['placement_2'] as $box)
                <mj-section padding-top="18px" padding-bottom="18px"> 

                    <mj-column vertical-align="middle">
                          @if (isset($box['title']))
                              <mj-text font-size="22px" >
                                
                                  <b>{{ $box['title'] }}</b> 
                              </mj-text>
                          @endif

                          @isset ($box['content'])                          
                              <mj-text  font-size="15px">{!! $box['content'] !!}</mj-text>
                          @endisset

                          @isset ($box['link'])
                              <mj-button css-class="marginauto-mobile" href="{{ $box['link'] }}" align="left">
                                  @isset ($box['link_title'])
                                      {{ $box['link_title'] }}
                                  @else
                                      {{ $box['link'] }}
                                  @endisset 
                              </mj-button> 
                          @endisset

                    </mj-column> 

                    @isset ($box['image'])
                        <mj-column vertical-align="middle"  padding="0"> 
                            <mj-image  
                                padding="0"
                                src="{{ 
                url( 'storage/newsletters/boxes/sizes/' . getFilenameWithoutExt($box['image']) . '-medium' . getExtension($box['image'])  )
                                }}" > 
                {{-- padding="0"  --}}
                            </mj-image>    
                        </mj-column>
                    @endisset

                </mj-section>

            @endforeach
        </mj-wrapper>
        
          
        @endif
 
        @include('emails.templates.mjml.partials.footer')
        
        {{-- Icons --}} 
        {{--  <mj-section background-color="#fbfbfb"></mj-section> --}} 
      
        
        {{--  Social icons --}} 
        {{-- <mj-section background-color="#f0f0f0"></mj-section> --}} 
        

  </mj-body>
</mjml>

<!DOCTYPE html>
<html lang="en">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="icon" href="https://fonts.gstatic.com/s/i/materialicons/school/v6/24px.svg" type="image/svg+xml">
      <title>SMP 3 Muhammadiyah | Presensi</title>
      <link href="{{asset('lte/vendors/bootstrap/dist/css/bootstrap.min.css')}}" rel="stylesheet">
      <link href="{{asset('lte/vendors/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet">
      <link href="{{asset('lte/vendors/nprogress/nprogress.css')}}" rel="stylesheet">
      <link href="{{asset('lte/vendors/animate.css/animate.min.css')}}" rel="stylesheet">
      <link href="{{asset('lte/build/css/custom.min.css')}}" rel="stylesheet">
      <meta name="robots" content="noindex, follow">
      <style>
         .footer {
            display: flex;
            justify-content: center;  /* Pusatkan secara horizontal */
            align-items: center;      /* Pusatkan secara vertikal */
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;              /* Pastikan footer selebar layar */
            height: 50px;             /* Atur tinggi footer sesuai kebutuhan */
            padding: 10px;
            background-color: #f5f5f5; /* Warna latar belakang footer */
            z-index: 1000;            /* Pastikan footer berada di atas elemen lain */
         }

         body {
            margin: 0;                /* Hapus margin body */
            padding-bottom: 60px;     /* Tambahkan padding bawah agar konten tidak tertutup oleh footer */
         }

         .created-by {
            font-size: 12px;
            font-family: 'Courier New', Courier, monospace;
            text-align: center;       /* Pusatkan teks */
         }


        .visi {
            font-size: 14px; 
            font-family: sans-serif;
        }

        #btn {
            background-color: rgb(42, 63, 84);
            color: #ffffff;
            padding-right: 50px;
            padding-left: 50px;
        }

        #btn:hover {
            background-color: rgb(58, 90, 122);
        }

        h1, p {
            color: rgb(42, 63, 84);
        }

        

      </style>
      <script nonce="215bc572-2e29-49ff-b54b-9a188732dd3e">try{(function(w,d){!function(j,k,l,m){if(j.zaraz)console.error("zaraz is loaded twice");else{j[l]=j[l]||{};j[l].executed=[];j.zaraz={deferred:[],listeners:[]};j.zaraz._v="5796";j.zaraz.q=[];j.zaraz._f=function(n){return async function(){var o=Array.prototype.slice.call(arguments);j.zaraz.q.push({m:n,a:o})}};for(const p of["track","set","debug"])j.zaraz[p]=j.zaraz._f(p);j.zaraz.init=()=>{var q=k.getElementsByTagName(m)[0],r=k.createElement(m),s=k.getElementsByTagName("title")[0];s&&(j[l].t=k.getElementsByTagName("title")[0].text);j[l].x=Math.random();j[l].w=j.screen.width;j[l].h=j.screen.height;j[l].j=j.innerHeight;j[l].e=j.innerWidth;j[l].l=j.location.href;j[l].r=k.referrer;j[l].k=j.screen.colorDepth;j[l].n=k.characterSet;j[l].o=(new Date).getTimezoneOffset();if(j.dataLayer)for(const w of Object.entries(Object.entries(dataLayer).reduce(((x,y)=>({...x[1],...y[1]})),{})))zaraz.set(w[0],w[1],{scope:"page"});j[l].q=[];for(;j.zaraz.q.length;){const z=j.zaraz.q.shift();j[l].q.push(z)}r.defer=!0;for(const A of[localStorage,sessionStorage])Object.keys(A||{}).filter((C=>C.startsWith("_zaraz_"))).forEach((B=>{try{j[l]["z_"+B.slice(7)]=JSON.parse(A.getItem(B))}catch{j[l]["z_"+B.slice(7)]=A.getItem(B)}}));r.referrerPolicy="origin";r.src="/cdn-cgi/zaraz/s.js?z="+btoa(encodeURIComponent(JSON.stringify(j[l])));q.parentNode.insertBefore(r,q)};["complete","interactive"].includes(k.readyState)?zaraz.init():j.addEventListener("DOMContentLoaded",zaraz.init)}}(w,d,"zarazData","script");window.zaraz._p=async bv=>new Promise((bw=>{if(bv){bv.e&&bv.e.forEach((bx=>{try{const by=d.querySelector("script[nonce]"),bz=by?.nonce||by?.getAttribute("nonce"),bA=d.createElement("script");bz&&(bA.nonce=bz);bA.innerHTML=bx;bA.onload=()=>{d.head.removeChild(bA)};d.head.appendChild(bA)}catch(bB){console.error(`Error executing script: ${bx}\n`,bB)}}));Promise.allSettled((bv.f||[]).map((bC=>fetch(bC[0],bC[1]))))}bw()}));zaraz._p({"e":["(function(w,d){})(window,document)"]});})(window,document)}catch(e){throw fetch("/cdn-cgi/zaraz/t"),e;};</script>
   </head>
   <body class="login">
      <div>
         <a class="hiddenanchor" id="signup"></a>
         <a class="hiddenanchor" id="signin"></a>
         <div class="login_wrapper">
            <div class="animate form login_form">
               <section class="login_content">
                  <form method="POST" action="{{ route('login') }}">
                     @csrf
                     <h1>Login Form</h1>
                     <!-- Alert Info Akun Demo -->
                     <div class="alert alert-info demo-alert" role="alert">
                        <strong><i class="fa fa-info-circle"></i> Akun Demo:</strong><br>
                        • <strong>Admin:</strong> <code>admin@example.com</code><br>
                        • <strong>Teacher:</strong> <code>teacher@example.com</code><br>
                        • <strong>Student:</strong> <code>student@example.com</code><br>
                        • <strong>Password:</strong> <code>12345678</code>
                     </div>
                     <div> 
                        <x-input-label for="email"/>
                        <x-text-input id="email" class="block mt-1 w-full" class="form-control" type="email" name="email" :value="old('email')" placeholder="Email" required autofocus autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                     </div>
                     <div>
                        <x-input-label for="password"/>
                        <x-text-input id="password" class="block mt-1 w-full" class="form-control" placeholder="Password"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                     </div>
                     <div>
                        <button id="btn" class="btn btn-default" submit>Log in</button>
                     </div>
                     <div class="clearfix"></div>
                     <div class="separator">
                        
                        <div class="clearfix"></div>
                        <br/>
                        <div>
                           <h1><i class="fa fa-graduation-cap"></i> SMP 3 Muhammadiyah </h1>
                           <p class="visi">Terwujudnya civitas akademika SMP Muhammadiyah 3 Bandung yang Unggul dalam Ilmu Pengetahuan berlandaskan Iman dan Taqwa</p>
                        </div>
                     </div>
                  </form>
               </section>
               <div class="footer">
                <p class="created-by">Crafted with <i class="fa fa-heart"></i> by <a href="https://www.linkedin.com/in/setiyawt/" target="_blank"> setiyawt</a></p>
               </div>
            </div>
         
        </div>
           
      </div>
      <script defer src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015" integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ==" data-cf-beacon='{"rayId":"8beb957e68924c59","serverTiming":{"name":{"cfL4":true}},"version":"2024.8.0","token":"cd0b4b3a733644fc843ef0b185f98241"}' crossorigin="anonymous"></script>
   </body>
</html>
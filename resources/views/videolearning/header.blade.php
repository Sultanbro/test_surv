<nav class="navbar navbar-expand-lg mb-0 v-nav">
     
	 	 <!-- <a href="/videolearning" class="logo" class="navbar-brand mr-auto mr-lg-0">
			<img src="/admin/images/logo.png" alt="" class="img-fluid">
		</a> -->
	 
      <button class="navbar-toggler p-0 border-0" type="button" data-toggle="offcanvas">
        <svg viewBox="0 0 512 512">
        <path d="M492,236H20c-11.046,0-20,8.954-20,20c0,11.046,8.954,20,20,20h472c11.046,0,20-8.954,20-20S503.046,236,492,236z"/>
        <path d="M492,76H20C8.954,76,0,84.954,0,96s8.954,20,20,20h472c11.046,0,20-8.954,20-20S503.046,76,492,76z"/>
        <path d="M492,396H20c-11.046,0-20,8.954-20,20c0,11.046,8.954,20,20,20h472c11.046,0,20-8.954,20-20
              C512,404.954,503.046,396,492,396z"/>
        </svg>
      </button>

      <div class="navbar-collapse offcanvas-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
  

          <li class="nav-item active">
            <a class="nav-link" href="/videolearning/1">Обучение продажам<span class="sr-only"></span></a>
          </li>

          <li class="nav-item active">
            <a class="nav-link" href="/videolearning/2">Управление<span class="sr-only"></span></a>
          </li>

          <li class="nav-item active">
            <a class="nav-link" href="/videolearning/5">Рекрутинг<span class="sr-only"></span></a>
          </li>

          <li class="nav-item active">
            <a class="nav-link" href="/videolearning/6">РОПу<span class="sr-only"></span></a>
          </li>

      
          <!-- <li class="nav-item">
            <a class="nav-link" href="/">Мой профиль</a>
          </li>
          <li class="nav-item droplink">
            <a class="nav-link" href="/videolearning/">Категории</a>

            
            <ul class="droplink-menu">

              @foreach($cats as $cat)
              <li class="nav-item">
                <a href="/videolearning/{{ $cat->id }}" class="nav-link">{{ $cat->title }}</a>
              </li>
              @endforeach
                
              <li class="nav-item sublink">
                <a href="#" class="nav-link">Тестовая категория</a>
                <ul class="sublink-menu">
                  <li class="nav-item">
                    <a class="nav-link" href="/videolearning/">Подкатегория 1</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="/videolearning/">Подкатегория 2</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="/videolearning/">Подкатегория 3</a>
                  </li>
                </ul>
              </li> -->
            </ul>
            
          </li>
        </ul>
        <form class="form-inline my-2 my-lg-0">
          <input class="form-control mr-sm-2" type="text" placeholder="Название видео..." aria-label="Search">
          <button class="btn btn-primary my-2 my-sm-0 search" type="submit">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg> Поиск
          </button>
        </form>
      </div>
    </nav>


	<style>
    .v-nav.navbar .navbar-nav li {
      width: unset;
    }

    .v-nav.navbar .navbar-nav li>a {
      color: #505050 !important;
    }
    .droplink .droplink-menu{
      z-index: 99;
      padding-top: 0;
    }
    .v-nav.navbar .navbar-nav .droplink-menu .nav-item{
      width: 250px;
    }
  </style>
<style>
.navbar.navbar-expand-sm.navbar-default {
  background: transparent;
  border: none !important
}
.logo {
	width: 70px;display: block;
}
.block {
  background: #fff;
}

html,
body {
  overflow-x: hidden; /* Prevent scroll on narrow devices */
}

.btn,
.form-control {
  font-size: 14px;
}
.search svg {
  width: 16px;
    top: 3px;
    left: 0;
    margin-right: 4px;
    position: relative;
}
.text-white-50 { color: rgba(255, 255, 255, .5); }

.bg-purple { background-color: #6f42c1; }

.lh-100 { line-height: 1; }
.lh-125 { line-height: 1.25; }
.lh-150 { line-height: 1.5; }


/****************** */
.wrap {
  display: flex;
  justify-content: space-between;
  position: relative;
  width: 1500px;
  margin: 0 auto;
}
.wrap .leftblock {
  width: 300px;
    background: #fff;
    margin-left: 15px;
    top: 66px;
    max-height: 100%;
    max-height: calc(100vh - 100px);
    overflow: auto;
    margin-top: 20px;
}
.wrap .rightblock {

    padding: 20px 5px;
    margin: 20px;
    margin-top: 20px;
    background: #fff;
}
.wrap .leftblock h3{
  font-size: 16px;
    padding: 15px 20px 5px;
    font-weight: 600;
}
.backlink {
  padding: 15px 20px;
    /* background: #f1f1f1; */
    display: block;
    font-size: 14px;
    color: #252525;
    fill: #252525;
    border-bottom: 1px solid #f1f1f1;
    text-decoration: none !important;
}
.backlink svg {
  width: 12px;
    position: relative;
    top: 1px;
    margin-right: 2px;
}
.box {
  padding: 15px;
}
/******************** */

/* */

::-webkit-scrollbar {width: 7px;}
::-webkit-scrollbar-track {background: #f9f9f9; }
::-webkit-scrollbar-thumb {border-radius: 5px;background: #888; } 
::-webkit-scrollbar-thumb:hover {background: #007bff; }

/**/
body {
  background: #f0f2f5;
}

.navbar {height: 66px;padding: 10px 25px;border-bottom: 1px solid #e5e7eb;background: #fff;}


* {
  font-family: 'Open Sans', sans-serif;
}


.nav-link {
  color: #515151;
    font-size: 14px;
    font-weight: 400;
    letter-spacing: 0.3px;
}
.logo {
  margin-right: 15px;
}
.list-group-item:first-child,
.list-group-item:last-child {
  border-radius: 9px
}
.list-group {
    padding: 15px;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-orient: vertical;
    border-radius: 17px;
    /* -webkit-box-direction: normal; */
    -ms-flex-direction: column;
    flex-direction: column;
    /* padding-left: 0; */
    margin-bottom: 0;
}
.navbar-toggler svg {
  width: 20px;
  outline:0 !important;
}
.rightblock .col-lg-4 {
  padding-left: 5px;
}
@media (max-width: 1023px) {
  .navbar-collapse.show {
    left: 0;  
  }
  .rightblock .col-lg-4 {
    padding-left: 15px;
  }
  .navbar-collapse {
    background: #fff;
    position: fixed;
    width: 100%;
    height: 100%;
    left: -100%;
    top: 66px;
    padding: 25px;
    transition: all 0.5s ease;
  }
}
@media (min-width: 1400px) {
  .container {
    max-width: 1240px;
  }
}
@media (min-width: 1600px) {
  .container {
    max-width: 1340px;
  }
}

@media (min-width: 1700px) {
  .container {
    max-width: 1440px;
  } 
}
@media (min-width: 1900px) {
  .container {
    max-width: 1540px;
  }
}

.shadow {
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}


.videolink {
  display: flex;
    font-size: 14px;
    padding: 10px 0;
    color: #6c6c6c;
    cursor: pointer;
    justify-content: space-between;
    text-decoration: none !important;
}
.videolink:hover span {
  color: #1076b0;
}
.videolink span:last-child {
  font-weight: 700
} 
.card-header{
  padding: 0;
}
.card {
  border: 0;
}
.card-body {
    padding: 5px 15px;
}
.btn-block {
  white-space: unset;
  text-align: left;
  color: black;
    font-weight: 600;
}
.list-group-item {
  font-size: 14px;
    padding: 10px 15px;
    margin: 3px 0;
    border-color: transparent;
    background: unset;
    border-radius: 9px;
}
.block {
  overflow: hidden;
}
.br {
  border-radius: 0.25rem;
}
.description {
    padding: 15px 0 0;
}
.description .cat {
  display: block;
  font-size: 14px;
}
.description .text {
  font-size: 14px;
    padding: 15px 0;
}
.btn-svg svg {
  width: 16px;
    position: relative;
    margin-right: 3px;
    top: 2px;
}
.description h1 {
  font-size: 1.5em;
  font-weight: 700;
}
.description .views {
  margin-bottom: 0;
  font-size: 14px;
}
.list-group-item.active,
.btn-primary {
  background: #1076b0;
  border-color: #1076b0;
} 
.btn-primary:hover {
  background: #0092e2;
  border-color: #0092e2;
}
.droplink {
  position: relative;
}
.droplink .droplink-menu {
  position: absolute;
  top: 100%;
  background: #fff;
  display: none;
  transition: 0.5s ease all;
  padding-top: 15px;
  padding-left: 0; 
  list-style: none;
}
.droplink:hover .droplink-menu {
  display: block;
  margin-top: 0;
}
.droplink-menu .nav-item {
  border-top: 1px solid #f1f1f1;
  width: 250px;
  font-size: 13px;
}
.droplink-menu .nav-item a {
    padding: 10px 25px !important;
    display: block;
}
.droplink-menu .nav-item:hover {
  background: #f1f1f1;
}
.sublink {
  position: relative;
}
.sublink-menu {
  position: absolute;
  left: 100%;
  top: -1px;
  background: #fff;
  display: none;
  list-style: none;
  padding-left: 0;
}
.sublink:hover .sublink-menu {
  display: block;
}


.article {
  display: flex;
    text-decoration: none !important;
    color: #000;
    margin: 15px 0;
    padding-bottom: 15px;
    border-bottom: 1px solid #f6f6f6
}
.article:hover p{
  color: #000
}
.article * {
  text-decoration: none;
}
.article .left {
  width: 200px;
  flex: 0 0 200px;
  padding: 0;
}
.article .left img {
  border-radius: 9px;
}
.card-header {
  border-bottom: unset;
}
#accordion {
    border: 1px solid #f0f2f5;
    background: #ffffff;
    border-radius: 7px;
    overflow: hidden;
    min-height: 410px;
    box-shadow: 0 0 5px #f4f4f4 inset;
}
.bordero {
  border-radius: 9px;
  border: 1px solid #f1f1f1;
}
.paddingo {
  padding: 15px;
}
p.title {
  font-size: 14px;
  font-weight: 700;
}
.info p {
  font-size: 14px;
  margin-bottom: 0;
}
#player {
  border-radius: 8px;
  overflow: hidden
}
.card-header .btn {
  white-space: unset;
    text-align: left;
    color: black;
    font-weight: 600;
    padding: 10px 15px;
    border-bottom: 0;
}
.article .right {
  width: auto;
  padding: 0 15px;
}
.article .right h2 {
  font-size: 21px;
  font-weight: 600;
}
.article .right p {
  font-size: 13px;
  font-weight: 400;
}
.article .right p.cat {
  margin-bottom: 7px
}
h1.title {
  font-size: 1.5em;
  font-weight: 700;
  margin-bottom: 15px;
  padding: 0;
}

/*** */
#accordion .card {
  margin-bottom: 0;
}
.pagination {
  padding: 0 15px;
}
.pagination span,
.pagination a {
    position: relative;
    display: block;
    padding: .5rem .75rem;
    margin-left: -1px;
    line-height: 1.25;
    color: #1076b0;
    background-color: #fff;
    border: 1px solid #dee2e6;
}
.pagination li.active span {
    z-index: 1;
    color: #fff;
    background-color: #1076b0;
    border-color: #1076b0;
}
.h3 {
  position: relative;
  padding-right: 25px !important;
}
.h3 svg {
  display: none;
}
@media(max-width: 1023px) {
  .droplink,
  .droplink-menu {
    display: none !important;
  }
  .h3 {
    cursor: pointer;
    position: relative;
    padding-right: 35px !important;
  }
  .h3 svg {
    display: block;
    position: absolute;
    right: 24px;
    width: 12px;
    top: 18px;
    transition: 0.3s ease all;
    transform: rotate(180deg);
  }
  .h3 svg.rotate {
    transform: rotate(0deg);
  }
  .list-group {
    display: none;
  }
  .list-group.show {
    display: block;
  }
  .wrap {
    flex-direction: column;
  }
  .wrap .leftblock {
    width: 100%; 
    width: 100vw;
    position: static;
    margin: 0;
  }
  .wrap .rightblock {
    width: 100%;
 
    margin: 0;
    padding: 15px 0;
  }
  
}
@media(max-width: 576px) {
  .article {
    display: block;
    padding-bottom: 0;
  }
  .article .left {
    width: 100%;
    margin-bottom: 15px;
  }
  .article .right {
    padding: 0;
  }
  .article .right h2 {
    font-size: 16px;
  }
  .article .right p {
    font-size: 12px;
  }
}


.card-header {
    background-color: #f0f2f5;
    border-bottom: 0px solid #d8d8d8;
}





/*********************  */
/********* ADMIN  */
/*********************  */

.bg-grey {
  background: #fbfbfb;
}
.admin .h3 {
  padding-left: 20px;
}
.admin .h3 {
  font-size: 1.5em;
}
.admin .body .h3 {
  padding: 0;
  margin-bottom: 15px;
}
body.admin {
  padding-left: 280px;
}
.fh {
  min-height: 100vh;
    min-height: calc(100vh - 60px);
}
.fh .bg-grey {
  min-height: 100vh;
  min-height: calc(100vh - 60px);
}
.table-admin {
  padding: 30px 0; 
  font-size: 13px;
  border-left: 1px solid #dee2e6;
    border-right: 1px solid #dee2e6;
    border-bottom: 1px solid #dee2e6;
}
.table-admin td, .table-admin th {
  border-right: 1px solid #dee2e6;
}
.table-admin th:first-child,
.table-admin td:first-child {
  padding-left: 15px;
  width: 1px;
}
.table-admin .btn {
  padding: 1px 5px;
}
.table-admin img {
  width: 175px;
}
.admin .pagination {
  padding: 0;
}
.table-admin td:last-child, .table-admin th:last-child {padding: 0 10px;vertical-align:middle;width: 116px;}
.table-admin tr:nth-child(2n) {background: #f1f1f1;}
.table-admin tr:nth-child(2n +1) {background: #fff;}
.table-admin tr:first-child {background: #dee2e6;}
.admin .btn-p {
  padding: 1px 5px;
}
.admin textarea {
  min-height: 100px
}
.poster_count{
  position: relative;
    padding: 0 !important;
    width: 175px;
}
.poster_count span {
  position: absolute;
    top: 0;
    right: 0;
    display: flex;
    width: 40px;
    height: 100%;
    background: rgba(69,69,69,.71);
    font-size: 1.1rem;
    font-weight: 600;
    align-items: center;
    color: #fff;
    justify-content: center;
}
.videobox {
  padding: 15px;
  background: #f0f2f5;
}
#video {
  width: 100%;
  background: aliceblue; 
}
.admin label {
  font-weight: 700;
}
#status {
    position: relative;
}
#status svg {
    margin: auto;
    position: absolute;
    top: -12px;
    right: 20px;
    background: rgb(255, 255, 255);
}
</style> 

<style>
#left-panel.show-sidebar {
  padding: 0;
}
</style>
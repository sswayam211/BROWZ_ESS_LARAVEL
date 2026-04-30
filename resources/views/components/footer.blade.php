<div class="footer">
    <p class="text-center m-0">Copyright © 2025,<span class="text-green"> Icon Technology Projects &
            Services LLC.</span>
        All Rights
        Reserved.</p>
</div>
</div>
</div>
</main>

<footer>

</footer>



<!-- my js  -->
<script>
    try {
        let showrReqDropdown = document.querySelector('.show-req-dropdown');
        let dropMenu = document.querySelector('.drop-menu');
        let dropIcon = document.querySelector('.drop-icon');
        console.log(dropMenu.children.length);


        // console.log(showrReqDropdown.parentElement.clientHeight);


        showrReqDropdown.addEventListener('click', () => {
            if (dropMenu.style.height != '0px') {
                dropMenu.style.height = '0px';
                dropIcon.style.transform = 'rotate(0deg)';
            } else {
                dropMenu.style.height = (showrReqDropdown.parentElement.clientHeight * dropMenu.children.length) + 5 + 'px';
                dropIcon.style.transform = 'rotate(180deg)';
            }
        });

    } catch (err) {
        console.log(err);
    }


    // active dropdown — open if any child has `active` class
    // try {
    //     let dropMenu = document.querySelector('.drop-menu').querySelectorAll('li');
    //     let dropIcon = document.querySelector('.drop-icon');

    //     dropMenu.forEach(menu => {
    //         if (menu.classList.includes('active')) {
    //             console.log('1');
    //             dropMenu.style.height = (showrReqDropdown.parentElement.clientHeight * dropMenu.children.length) + 5 + 'px';
    //             dropIcon.style.transform = 'rotate(180deg)';
    //         } else {
    //             console.log('2');
    //         }
    //     });
    // } catch (err) {
    //     console.log(err);
    // }



    try {
        let headerHeight = document.querySelector('header').offsetHeight;
        let sideNav = document.querySelector('.side-nav');
        let windowHeight = document.documentElement.clientHeight;
        // let main = document.querySelector('.main');
        let footer = document.querySelector('.footer');

        sideNav.style.height = `${windowHeight - headerHeight-2}px`;
        // main.style.top = `${headerHeight}px`;
        // sideNav.style.top = `${headerHeight}px`;
        if (window.innerWidth > 767) {
            footer.style.left = `${sideNav.offsetWidth}px`;
        } else {
            footer.style.left = `${0}px`;
        }

        window.addEventListener('resize', () => {
            let newWindowHeight = document.documentElement.clientHeight;
            sideNav.style.height = `${newWindowHeight - headerHeight-2}px`;
            // sideNav.style.top = `${headerHeight}px`;
            if (window.innerWidth > 767) {
                sideNav.style.left = '0px';
                footer.style.left = `${sideNav.offsetWidth}px`;
            } else {
                sideNav.style.left = '-300px';
                footer.style.left = `${0}px`;
            }
        });

        // sticky side nav
        if (window.innerWidth > 767) {
            window.addEventListener('scroll', () => {
                let scrollTop = window.scrollY;
                sideNav.style.top = `${0 + scrollTop}px`;
            });
            sideNav.style.left = '0px';
        } else {
            sideNav.style.left = '-300px';
        }
    } catch (err) {
        console.log(err);
    }


    try {
        // header nav display and hide
        let button = document.querySelector('.logo button');
        let sideNavMenu = document.querySelector('.side-nav');
        let navVisible = false;
        button.addEventListener('click', () => {
            if (sideNavMenu.style.left == '-300px') {
                // sideNavMenu.classList.remove('d-none');
                // sideNavMenu.classList.add('d-block');
                // setTimeout(() => {
                sideNavMenu.style.left = '0';
                sideNavMenu.style.boxShadow = '0px 0px 15px black';
                navVisible = true;
                // }, 10);
            } else {
                sideNavMenu.style.left = '-300px';
                sideNavMenu.style.boxShadow = 'none';
                navVisible = false;
                // setTimeout(() => {
                //     sideNavMenu.classList.remove('d-block');
                //     sideNavMenu.classList.add('d-none');
                // }, 100);
            }
        });


        // hide nav when clicked out side nav bar
        window.addEventListener('click', (e) => {
            let sideNav = document.querySelector('.side-nav');
            let header = document.querySelector('header');
            if (window.innerWidth < 767) {
                if (navVisible === true) {
                    if (!sideNav.contains(e.target) && !header.contains(e.target)) {
                        sideNavMenu.style.left = '-300px';
                        sideNavMenu.style.boxShadow = 'none';
                    }
                }
            }
        });


        //navigation active class
        let navLinks = document.querySelectorAll('.side-nav li a');
        navLinks.forEach(link => {
            if (link.href === window.location.href) {
                link.parentElement.classList.add('active');
            } else {
                link.parentElement.classList.remove('active');
            }
        });
    } catch (err) {
        console.log(err);
    }


    // removing min height from panel body(datatable)
    try {
        document.querySelector('.panel-body').style.minHeight = 'max-content';
    } catch (err) {
        console.log(err);
    }


    try {
        let form = document.querySelector('form');
        form.addEventListener('submit', (el) => {
            let submitBtn = el.querySelector('button[type=submit]');
            setTimeout(() => {
                submitBtn.disabled = true;
                // submitBtn.style.cursor = 'not-allowed';
            }, 20);
        });
    } catch (err) {
        console.log(err);
    }


    // open nav dropdown when link is active
    try {
        let dropDown = document.querySelectorAll('.drop-down');
        dropDown.forEach(down => {
            let showrReqDropdownBtn = down.querySelector('.show-req-dropdown');
            let drop_body = down.querySelector('.drop-menu');
            let menu_links = down.querySelectorAll('.drop-menu li a');
            let drop_icon = down.querySelector('.drop-icon');
            let isOpen = false;
            // Highlight active link only and check if any is active
            let anyActive = false;
            menu_links.forEach(link => {
                if (link.href === window.location.href) {
                    link.parentElement.classList.add('active');
                    anyActive = true;
                } else {
                    link.parentElement.classList.remove('active');
                }
            });
            // If any child is active, open the dropdown
            if (anyActive) {
                drop_body.style.height = (showrReqDropdownBtn.parentElement.clientHeight * drop_body.children.length) + 8 + 'px';
                drop_icon.style.transform = 'rotate(180deg)';
                isOpen = true;
            } else {
                drop_body.style.height = '0px';
                drop_icon.style.transform = 'rotate(0deg)';
            }
            // Toggle open/close on button click
            if (showrReqDropdownBtn) {
                showrReqDropdownBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    isOpen = !isOpen;
                    if (isOpen) {
                        drop_body.style.height = (showrReqDropdownBtn.parentElement.clientHeight * drop_body.children.length) + 8 + 'px';
                        drop_icon.style.transform = 'rotate(180deg)';
                    } else {
                        drop_body.style.height = '0px';
                        drop_icon.style.transform = 'rotate(0deg)';
                    }
                });
            }
        });
    } catch (err) {
        console.log(err);
    }
</script>

</body>

</html>
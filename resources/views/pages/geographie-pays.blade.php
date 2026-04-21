<x-app-layout title="Geographie " is-sidebar-open="false" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Pays
          </h2>
          <div class="hidden h-full py-1 sm:flex">
            <div class="h-full w-px bg-slate-300 dark:bg-navy-600"></div>
          </div>
          <ul class="hidden flex-wrap items-center space-x-2 sm:flex">
            <li class="flex items-center space-x-2">
              <a
                class="text-primary transition-colors hover:text-primary-focus dark:text-accent-light dark:hover:text-accent"
                href="#"
                >Geographie</a
              >
              <svg
                x-ignore
                xmlns="http://www.w3.org/2000/svg"
                class="size-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 5l7 7-7 7"
                />
              </svg>
            </li>
            <li>Pays</li>
          </ul>



                  
  <div x-data="{showModal:false}">
    <button
      @click="showModal = true"
      class="btn bg-slate-150 font-medium text-slate-800 hover:bg-slate-200 focus:bg-slate-200 active:bg-slate-200/80 dark:bg-navy-500 dark:text-navy-50 dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450/90"
    >
      Activer un nouveau pays
    </button>
    <template x-teleport="#x-teleport-target">
      <div
        class="fixed inset-0 z-100 flex flex-col items-center justify-center overflow-hidden px-4 py-6 sm:px-5"
        x-show="showModal"
        role="dialog"
        @keydown.window.escape="showModal = false"
      >
        <div
          class="absolute inset-0 bg-slate-900/60 transition-opacity duration-300"
          @click="showModal = false"
          x-show="showModal"
          x-transition:enter="ease-out"
          x-transition:enter-start="opacity-0"
          x-transition:enter-end="opacity-100"
          x-transition:leave="ease-in"
          x-transition:leave-start="opacity-100"
          x-transition:leave-end="opacity-0"
        ></div>
        <div
          class="relative w-full max-w-lg origin-top rounded-lg bg-white transition-all duration-300 dark:bg-navy-700"
          x-show="showModal"
          x-transition:enter="easy-out"
          x-transition:enter-start="opacity-0 scale-95"
          x-transition:enter-end="opacity-100 scale-100"
          x-transition:leave="easy-in"
          x-transition:leave-start="opacity-100 scale-100"
          x-transition:leave-end="opacity-0 scale-95"
        >
          <div
            class="flex justify-between rounded-t-lg bg-slate-200 px-4 py-3 dark:bg-navy-800 sm:px-5"
          >
            <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">
             Formulaire d'enregistrement de pays
            </h3>
            <button
              @click="showModal = !showModal"
              class="btn -mr-1.5 size-7 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="size-4.5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M6 18L18 6M6 6l12 12"
                ></path>
              </svg>
            </button>
          </div>
          <div class="px-4 py-4 sm:px-5">
            <p>
              En remplissant ce formulaire, vous allez activer un nouveau pays dans lequel toutes les plateformes de la CJES seront accessibles
            </p>
            <div class="mt-4 space-y-4">
              
             
              <label class="block">
                <span>Nom du pays:</span>
                <input
                  class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                  placeholder="Exemple: Togo"
                  type="text"
                />
              </label>
             <label class="block">
                <span>Code du pays:</span>
                <input
                  class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                  placeholder="Exemple: TG"
                  type="text"
                />
              </label>
              <label class="block">
                <span>Indicatif téléphonique du pays:</span>
                <input
                  class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                  placeholder="Exemple: +228"
                  type="text"
                />
              </label>
              <label class="block">
                <span>Image du drapeau:</span>
                <input
                  class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                  placeholder="Exemple: Togo"
                  type="file"
                />
              </label>
              <div class="space-x-2 text-right">
                <button
                  @click="showModal = false"
                  class="btn min-w-[7rem] rounded-full border border-slate-300 font-medium text-slate-800 hover:bg-slate-150 focus:bg-slate-150 active:bg-slate-150/80 dark:border-navy-450 dark:text-navy-50 dark:hover:bg-navy-500 dark:focus:bg-navy-500 dark:active:bg-navy-500/90"
                >
                  Annuler
                </button>
                <button
                  @click="showModal = false"
                  class="btn min-w-[7rem] rounded-full bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90"
                >
                  Lancer la création
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
 

                
              


        </div>
        <div class="grid grid-cols-1 gap-4 sm:gap-5 lg:gap-6">
            

            <!-- GridJS Advanced Example -->
            <div class="card pb-4">
                <div class="my-3 flex h-8 items-center justify-between px-4 sm:px-5">
                    <h2 class="font-medium tracking-wide text-slate-700 line-clamp-1 dark:text-navy-100 lg:text-base">
                        GridJS Advanced Table
                    </h2>
                    <div x-data="usePopper({ placement: 'bottom-end', offset: 4 })" @click.outside="if(isShowPopper) isShowPopper = false" class="inline-flex">
                        <button x-ref="popperRef" @click="isShowPopper = !isShowPopper" class="btn size-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"></path>
                            </svg>
                        </button>

                        <div x-ref="popperRoot" class="popper-root" :class="isShowPopper &amp;&amp; 'show'" style="position: fixed; inset: auto 0px 0px auto; margin: 0px; transform: translate(-44px, 1090px);" data-popper-placement="top-end" data-popper-reference-hidden="" data-popper-escaped="">
                            <div class="popper-box rounded-md border border-slate-150 bg-white py-1.5 font-inter dark:border-navy-500 dark:bg-navy-700">
                                <ul>
                                    <li>
                                        <a href="#" class="flex h-8 items-center px-3 pr-8 font-medium tracking-wide outline-hidden transition-all hover:bg-slate-100 hover:text-slate-800 focus:bg-slate-100 focus:text-slate-800 dark:hover:bg-navy-600 dark:hover:text-navy-100 dark:focus:bg-navy-600 dark:focus:text-navy-100">Action</a>
                                    </li>
                                    <li>
                                        <a href="#" class="flex h-8 items-center px-3 pr-8 font-medium tracking-wide outline-hidden transition-all hover:bg-slate-100 hover:text-slate-800 focus:bg-slate-100 focus:text-slate-800 dark:hover:bg-navy-600 dark:hover:text-navy-100 dark:focus:bg-navy-600 dark:focus:text-navy-100">Another
                                            Action</a>
                                    </li>
                                    <li>
                                        <a href="#" class="flex h-8 items-center px-3 pr-8 font-medium tracking-wide outline-hidden transition-all hover:bg-slate-100 hover:text-slate-800 focus:bg-slate-100 focus:text-slate-800 dark:hover:bg-navy-600 dark:hover:text-navy-100 dark:focus:bg-navy-600 dark:focus:text-navy-100">Something
                                            else</a>
                                    </li>
                                </ul>
                                <div class="my-1 h-px bg-slate-150 dark:bg-navy-500"></div>
                                <ul>
                                    <li>
                                        <a href="#" class="flex h-8 items-center px-3 pr-8 font-medium tracking-wide outline-hidden transition-all hover:bg-slate-100 hover:text-slate-800 focus:bg-slate-100 focus:text-slate-800 dark:hover:bg-navy-600 dark:hover:text-navy-100 dark:focus:bg-navy-600 dark:focus:text-navy-100">Separated
                                            Link</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <div x-data="pages.tables.initGridTableExapmle"><div role="complementary" class="gridjs gridjs-container" style="width: 100%;"><div class="gridjs-head"><div class="gridjs-search"><input type="search" placeholder="Type a keyword..." aria-label="Type a keyword..." class="gridjs-input gridjs-search-input" value=""></div></div><div class="gridjs-wrapper" style="height: auto;"><table role="grid" class="gridjs-table" style="height: auto;"><thead class="gridjs-thead"><tr class="gridjs-tr"><th data-column-id="id" class="gridjs-th gridjs-th-sort" tabindex="0" style="min-width: 61px; width: 113px;"><div class="gridjs-th-content">ID</div><button tabindex="-1" aria-label="Sort column ascending" title="Sort column ascending" class="gridjs-sort gridjs-sort-neutral"></button></th><th data-column-id="name" class="gridjs-th gridjs-th-sort" tabindex="0" style="min-width: 84px; width: 154px;"><div class="gridjs-th-content">Name</div><button tabindex="-1" aria-label="Sort column ascending" title="Sort column ascending" class="gridjs-sort gridjs-sort-neutral"></button></th><th data-column-id="avatar_url" class="gridjs-th" style="min-width: 89px; width: 163px;"><div class="gridjs-th-content">Avatar</div></th><th data-column-id="email" class="gridjs-th gridjs-th-sort" tabindex="0" style="min-width: 193px; width: 354px;"><div class="gridjs-th-content">Email</div><button tabindex="-1" aria-label="Sort column ascending" title="Sort column ascending" class="gridjs-sort gridjs-sort-neutral"></button></th><th data-column-id="phone" class="gridjs-th gridjs-th-sort" tabindex="0" style="min-width: 153px; width: 280px;"><div class="gridjs-th-content">Phone Number</div><button tabindex="-1" aria-label="Sort column ascending" title="Sort column ascending" class="gridjs-sort gridjs-sort-neutral"></button></th><th data-column-id="actions" class="gridjs-th" style="min-width: 104px; width: 191px;"><div class="gridjs-th-content">Actions</div></th></tr></thead><tbody class="gridjs-tbody"><tr class="gridjs-tr"><td data-column-id="id" class="gridjs-td"><span><span class="mx-2">1</span></span></td><td data-column-id="name" class="gridjs-td"><span><span class="text-slate-700 dark:text-navy-100 font-medium">John</span></span></td><td data-column-id="avatar_url" class="gridjs-td"><span><div class="avatar flex">
                                    <img class="rounded-full" src="/images/200x200.png" alt="avatar">
                                </div></span></td><td data-column-id="email" class="gridjs-td">john@example.com</td><td data-column-id="phone" class="gridjs-td">(01) 22 888 4444</td><td data-column-id="actions" class="gridjs-td"><span><div class="flex justify-center space-x-2">
                            <button @click="editItem" class="btn size-8 p-0 text-info hover:bg-info/20 focus:bg-info/20 active:bg-info/25">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button @click="deleteItem" class="btn size-8 p-0 text-error hover:bg-error/20 focus:bg-error/20 active:bg-error/25">
                                <i class="fa fa-trash-alt"></i>
                            </button>
                        </div></span></td></tr><tr class="gridjs-tr"><td data-column-id="id" class="gridjs-td"><span><span class="mx-2">2</span></span></td><td data-column-id="name" class="gridjs-td"><span><span class="text-slate-700 dark:text-navy-100 font-medium">Doe</span></span></td><td data-column-id="avatar_url" class="gridjs-td"><span><div class="avatar flex">
                                    <img class="rounded-full" src="/images/200x200.png" alt="avatar">
                                </div></span></td><td data-column-id="email" class="gridjs-td">thedoe@example.com</td><td data-column-id="phone" class="gridjs-td">(33) 22 888 4444</td><td data-column-id="actions" class="gridjs-td"><span><div class="flex justify-center space-x-2">
                            <button @click="editItem" class="btn size-8 p-0 text-info hover:bg-info/20 focus:bg-info/20 active:bg-info/25">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button @click="deleteItem" class="btn size-8 p-0 text-error hover:bg-error/20 focus:bg-error/20 active:bg-error/25">
                                <i class="fa fa-trash-alt"></i>
                            </button>
                        </div></span></td></tr><tr class="gridjs-tr"><td data-column-id="id" class="gridjs-td"><span><span class="mx-2">3</span></span></td><td data-column-id="name" class="gridjs-td"><span><span class="text-slate-700 dark:text-navy-100 font-medium">Nancy</span></span></td><td data-column-id="avatar_url" class="gridjs-td"><span><div class="avatar flex">
                                    <img class="rounded-full" src="/images/200x200.png" alt="avatar">
                                </div></span></td><td data-column-id="email" class="gridjs-td">nancy@example.com</td><td data-column-id="phone" class="gridjs-td">(21) 33 888 4444</td><td data-column-id="actions" class="gridjs-td"><span><div class="flex justify-center space-x-2">
                            <button @click="editItem" class="btn size-8 p-0 text-info hover:bg-info/20 focus:bg-info/20 active:bg-info/25">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button @click="deleteItem" class="btn size-8 p-0 text-error hover:bg-error/20 focus:bg-error/20 active:bg-error/25">
                                <i class="fa fa-trash-alt"></i>
                            </button>
                        </div></span></td></tr><tr class="gridjs-tr"><td data-column-id="id" class="gridjs-td"><span><span class="mx-2">4</span></span></td><td data-column-id="name" class="gridjs-td"><span><span class="text-slate-700 dark:text-navy-100 font-medium">Clarke</span></span></td><td data-column-id="avatar_url" class="gridjs-td"><span><div class="avatar flex">
                                    <img class="rounded-full" src="/images/200x200.png" alt="avatar">
                                </div></span></td><td data-column-id="email" class="gridjs-td">clarke@example.com</td><td data-column-id="phone" class="gridjs-td">(44) 33 888 4444</td><td data-column-id="actions" class="gridjs-td"><span><div class="flex justify-center space-x-2">
                            <button @click="editItem" class="btn size-8 p-0 text-info hover:bg-info/20 focus:bg-info/20 active:bg-info/25">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button @click="deleteItem" class="btn size-8 p-0 text-error hover:bg-error/20 focus:bg-error/20 active:bg-error/25">
                                <i class="fa fa-trash-alt"></i>
                            </button>
                        </div></span></td></tr><tr class="gridjs-tr"><td data-column-id="id" class="gridjs-td"><span><span class="mx-2">5</span></span></td><td data-column-id="name" class="gridjs-td"><span><span class="text-slate-700 dark:text-navy-100 font-medium">Robert</span></span></td><td data-column-id="avatar_url" class="gridjs-td"><span><div class="avatar flex">
                                    <img class="rounded-full" src="/images/200x200.png" alt="avatar">
                                </div></span></td><td data-column-id="email" class="gridjs-td">robert@example.com</td><td data-column-id="phone" class="gridjs-td">(27) 63 688 6444</td><td data-column-id="actions" class="gridjs-td"><span><div class="flex justify-center space-x-2">
                            <button @click="editItem" class="btn size-8 p-0 text-info hover:bg-info/20 focus:bg-info/20 active:bg-info/25">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button @click="deleteItem" class="btn size-8 p-0 text-error hover:bg-error/20 focus:bg-error/20 active:bg-error/25">
                                <i class="fa fa-trash-alt"></i>
                            </button>
                        </div></span></td></tr><tr class="gridjs-tr"><td data-column-id="id" class="gridjs-td"><span><span class="mx-2">6</span></span></td><td data-column-id="name" class="gridjs-td"><span><span class="text-slate-700 dark:text-navy-100 font-medium">Tom</span></span></td><td data-column-id="avatar_url" class="gridjs-td"><span><div class="avatar flex">
                                    <img class="rounded-full" src="/images/200x200.png" alt="avatar">
                                </div></span></td><td data-column-id="email" class="gridjs-td">thetom@example.com</td><td data-column-id="phone" class="gridjs-td">(57) 63 688 6444</td><td data-column-id="actions" class="gridjs-td"><span><div class="flex justify-center space-x-2">
                            <button @click="editItem" class="btn size-8 p-0 text-info hover:bg-info/20 focus:bg-info/20 active:bg-info/25">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button @click="deleteItem" class="btn size-8 p-0 text-error hover:bg-error/20 focus:bg-error/20 active:bg-error/25">
                                <i class="fa fa-trash-alt"></i>
                            </button>
                        </div></span></td></tr><tr class="gridjs-tr"><td data-column-id="id" class="gridjs-td"><span><span class="mx-2">7</span></span></td><td data-column-id="name" class="gridjs-td"><span><span class="text-slate-700 dark:text-navy-100 font-medium">Nolan</span></span></td><td data-column-id="avatar_url" class="gridjs-td"><span><div class="avatar flex">
                                    <img class="rounded-full" src="/images/200x200.png" alt="avatar">
                                </div></span></td><td data-column-id="email" class="gridjs-td">Nolan@example.com</td><td data-column-id="phone" class="gridjs-td">(27) 63 688 6444</td><td data-column-id="actions" class="gridjs-td"><span><div class="flex justify-center space-x-2">
                            <button @click="editItem" class="btn size-8 p-0 text-info hover:bg-info/20 focus:bg-info/20 active:bg-info/25">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button @click="deleteItem" class="btn size-8 p-0 text-error hover:bg-error/20 focus:bg-error/20 active:bg-error/25">
                                <i class="fa fa-trash-alt"></i>
                            </button>
                        </div></span></td></tr><tr class="gridjs-tr"><td data-column-id="id" class="gridjs-td"><span><span class="mx-2">8</span></span></td><td data-column-id="name" class="gridjs-td"><span><span class="text-slate-700 dark:text-navy-100 font-medium">Adam</span></span></td><td data-column-id="avatar_url" class="gridjs-td"><span><div class="avatar flex">
                                    <img class="rounded-full" src="/images/200x200.png" alt="avatar">
                                </div></span></td><td data-column-id="email" class="gridjs-td">Adam@example.com</td><td data-column-id="phone" class="gridjs-td">(12) 22 888 4444</td><td data-column-id="actions" class="gridjs-td"><span><div class="flex justify-center space-x-2">
                            <button @click="editItem" class="btn size-8 p-0 text-info hover:bg-info/20 focus:bg-info/20 active:bg-info/25">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button @click="deleteItem" class="btn size-8 p-0 text-error hover:bg-error/20 focus:bg-error/20 active:bg-error/25">
                                <i class="fa fa-trash-alt"></i>
                            </button>
                        </div></span></td></tr><tr class="gridjs-tr"><td data-column-id="id" class="gridjs-td"><span><span class="mx-2">9</span></span></td><td data-column-id="name" class="gridjs-td"><span><span class="text-slate-700 dark:text-navy-100 font-medium">Glen</span></span></td><td data-column-id="avatar_url" class="gridjs-td"><span><div class="avatar flex">
                                    <img class="rounded-full" src="/images/200x200.png" alt="avatar">
                                </div></span></td><td data-column-id="email" class="gridjs-td">Glen@example.com</td><td data-column-id="phone" class="gridjs-td">(74) 22 888 4444</td><td data-column-id="actions" class="gridjs-td"><span><div class="flex justify-center space-x-2">
                            <button @click="editItem" class="btn size-8 p-0 text-info hover:bg-info/20 focus:bg-info/20 active:bg-info/25">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button @click="deleteItem" class="btn size-8 p-0 text-error hover:bg-error/20 focus:bg-error/20 active:bg-error/25">
                                <i class="fa fa-trash-alt"></i>
                            </button>
                        </div></span></td></tr><tr class="gridjs-tr"><td data-column-id="id" class="gridjs-td"><span><span class="mx-2">10</span></span></td><td data-column-id="name" class="gridjs-td"><span><span class="text-slate-700 dark:text-navy-100 font-medium">Edna</span></span></td><td data-column-id="avatar_url" class="gridjs-td"><span><div class="avatar flex">
                                    <img class="rounded-full" src="/images/200x200.png" alt="avatar">
                                </div></span></td><td data-column-id="email" class="gridjs-td">Edna@example.com</td><td data-column-id="phone" class="gridjs-td">(52) 33 888 4444</td><td data-column-id="actions" class="gridjs-td"><span><div class="flex justify-center space-x-2">
                            <button @click="editItem" class="btn size-8 p-0 text-info hover:bg-info/20 focus:bg-info/20 active:bg-info/25">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button @click="deleteItem" class="btn size-8 p-0 text-error hover:bg-error/20 focus:bg-error/20 active:bg-error/25">
                                <i class="fa fa-trash-alt"></i>
                            </button>
                        </div></span></td></tr><tr class="gridjs-tr"><td role="alert" colspan="6" class="gridjs-td gridjs-message gridjs-error">An error happened while fetching the data</td></tr></tbody></table></div><div class="gridjs-footer"><div class="gridjs-pagination"><div class="gridjs-pages"><button tabindex="0" role="button" disabled="" title="Previous" aria-label="Previous" class="">Previous</button><button tabindex="0" role="button" title="Next" aria-label="Next" class="" disabled="">Next</button></div></div></div><div id="gridjs-temp" class="gridjs-temp"></div></div></div>
                </div>
            </div>
        </div>
        
      </main>
</x-app-layout>

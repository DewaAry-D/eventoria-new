<div class="w-full p-md sm:p-lg bg-surface-container-lowest rounded-3xl border border-outline-variant/30 shadow-card flex flex-col gap-md select-none animate-fade-in">
    
    <div class="flex items-center gap-sm text-primary border-b border-surface-container/60 pb-xs">
        <svg class="w-5 h-5 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/>
        </svg>
        <h4 class="text-title-sm font-bold tracking-tight">Media Sosial</h4>
    </div>

    <div class="grid grid-cols-2 gap-sm w-full pt-xs">
        
        @if(!empty($org->ig_url))
            <a href="{{ $org->ig_url }}" 
                target="_blank" 
                rel="noopener noreferrer"
                class="flex flex-col items-center justify-center p-md bg-surface-container-low border border-outline-variant/10 rounded-2xl gap-sm text-center group transition-all duration-300 hover:bg-primary/[0.03] hover:border-primary/20 active:scale-95 cursor-pointer shadow-2xs">
                
                <div class="w-10 h-10 rounded-full bg-surface-container-lowest shadow-sm flex items-center justify-center text-on-surface group-hover:text-primary transition-colors">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 1.8025C12.67 1.8025 12.9867 1.8125 14.0417 1.86083C15.18 1.9125 16.2358 2.1375 17.0483 2.95083C17.8608 3.76333 18.0867 4.81917 18.1383 5.9575C18.1867 7.0125 18.1967 7.32917 18.1967 9.99917C18.1967 12.6692 18.1867 12.9858 18.1383 14.0408C18.0867 15.1792 17.8617 16.235 17.0483 17.0475C16.2358 17.86 15.18 18.0858 14.0417 18.1375C12.9867 18.1858 12.67 18.1958 10 18.1958C7.33 18.1958 7.01333 18.1858 5.95833 18.1375C4.82 18.0858 3.76417 17.8608 2.95167 17.0475C2.13917 16.235 1.91333 15.1792 1.86167 14.0408C1.81333 12.9858 1.80333 12.6692 1.80333 9.99917C1.80333 7.32917 1.81333 7.0125 1.86167 5.9575C1.91333 4.81917 2.13833 3.76333 2.95167 2.95083C3.76417 2.13833 4.82 1.9125 5.95833 1.86083C7.01333 1.8125 7.33 1.8025 10 1.8025ZM10 0C7.28417 0 6.94417 0.0116667 5.8775 0.06C2.24583 0.226667 0.2275 2.24167 0.0608333 5.87667C0.0116667 6.94417 0 7.28417 0 10C0 12.7158 0.0116667 13.0558 0.06 14.1225C0.226667 17.7542 2.24167 19.7725 5.87667 19.9392C6.94417 19.9875 7.28417 19.9992 10 19.9992C12.7158 19.9992 13.0558 19.9875 14.1225 19.9392C17.7542 19.9392 19.7725 17.7542 19.9392 14.1225C19.9875 13.055 19.9992 12.715 19.9992 9.99917C19.9992 7.28333 19.9875 6.94333 19.9392 5.87667C19.7725 2.245 17.7575 0.226667 14.1225 0.06C13.055 0.0116667 12.715 0 9.99917 0L10 0ZM10 4.865C7.16417 4.865 4.865 7.16417 4.865 10C4.865 12.8358 7.16417 15.135 10 15.135C12.8358 15.135 15.135 12.8358 15.135 10C15.135 7.16417 12.8358 4.865 10 4.865ZM10 13.3333C8.15917 13.3333 6.66667 11.8408 6.66667 10C6.66667 8.15917 8.15917 6.66667 10 6.66667C11.8408 6.66667 13.3333 8.15917 13.3333 10C13.3333 11.8408 11.8408 13.3333 10 13.3333ZM15.3383 3.4625C14.675 3.4625 14.1375 4 14.1375 4.6625C14.1375 5.325 14.675 5.8625 15.3383 5.8625C16.0008 5.8625 16.5375 5.325 16.5375 4.6625C16.5375 4 16.0008 3.4625 15.3383 3.4625Z" fill="currentColor"/>
                    </svg>
                </div>
                <span class="text-body-sm font-bold text-primary tracking-tight">Instagram</span>
            </a>
        @else
            <div class="flex flex-col items-center justify-center p-md bg-surface-container/30 border border-outline-variant/10 rounded-2xl gap-sm text-center shadow-2xs opacity-40 cursor-not-allowed" 
                    title="Tidak melampirkan akun Instagram">
                <div class="w-10 h-10 rounded-full bg-surface-container-low flex items-center justify-center text-secondary/40">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 1.8025C12.67 1.8025 12.9867 1.8125 14.0417 1.86083C15.18 1.9125 16.2358 2.1375 17.0483 2.95083C17.8608 3.76333 18.0867 4.81917 18.1383 5.9575C18.1867 7.0125 18.1967 7.32917 18.1967 9.99917C18.1967 12.6692 18.1867 12.9858 18.1383 14.0408C18.0867 15.1792 17.8617 16.235 17.0483 17.0475C16.2358 17.86 15.18 18.0858 14.0417 18.1375C12.9867 18.1858 12.67 18.1958 10 18.1958C7.33 18.1958 7.01333 18.1858 5.95833 18.1375C4.82 18.0858 3.76417 17.8608 2.95167 17.0475C2.13917 16.235 1.91333 15.1792 1.86167 14.0408C1.81333 12.9858 1.80333 12.6692 1.80333 9.99917C1.80333 7.32917 1.81333 7.0125 1.86167 5.9575C1.91333 4.81917 2.13833 3.76333 2.95167 2.95083C3.76417 2.13833 4.82 1.9125 5.95833 1.86083C7.01333 1.8125 7.33 1.8025 10 1.8025ZM10 0C7.28417 0 6.94417 0.0116667 5.8775 0.06C2.24583 0.226667 0.2275 2.24167 0.0608333 5.87667C0.0116667 6.94417 0 7.28417 0 10C0 12.7158 0.0116667 13.0558 0.06 14.1225C0.226667 17.7542 2.24167 19.7725 5.87667 19.9392C6.94417 19.9875 7.28417 19.9992 10 19.9992C12.7158 19.9992 13.0558 19.9875 14.1225 19.9392C17.7542 19.9392 19.7725 17.7542 19.9392 14.1225C19.9875 13.055 19.9992 12.715 19.9992 9.99917C19.9992 7.28333 19.9875 6.94333 19.9392 5.87667C19.7725 2.245 17.7575 0.226667 14.1225 0.06C13.055 0.0116667 12.715 0 9.99917 0L10 0ZM10 4.865C7.16417 4.865 4.865 7.16417 4.865 10C4.865 12.8358 7.16417 15.135 10 15.135C12.8358 15.135 15.135 12.8358 15.135 10C15.135 7.16417 12.8358 4.865 10 4.865ZM10 13.3333C8.15917 13.3333 6.66667 11.8408 6.66667 10C6.66667 8.15917 8.15917 6.66667 10 6.66667C11.8408 6.66667 13.3333 8.15917 13.3333 10C13.3333 11.8408 11.8408 13.3333 10 13.3333ZM15.3383 3.4625C14.675 3.4625 14.1375 4 14.1375 4.6625C14.1375 5.325 14.675 5.8625 15.3383 5.8625C16.0008 5.8625 16.5375 5.325 16.5375 4.6625C16.5375 4 16.0008 3.4625 15.3383 3.4625Z" fill="currentColor"/>
                    </svg>
                </div>
                <span class="text-body-sm font-bold text-secondary/40 tracking-tight">Instagram</span>
            </div>
        @endif

        @if(!empty($org->linkedin_url))
            <a href="{{ $org->linkedin_url }}" 
                target="_blank" 
                rel="noopener noreferrer"
                class="flex flex-col items-center justify-center p-md bg-surface-container-low border border-outline-variant/10 rounded-2xl gap-sm text-center group transition-all duration-300 hover:bg-primary/[0.03] hover:border-primary/20 active:scale-95 cursor-pointer shadow-2xs">
                
                <div class="w-10 h-10 rounded-full bg-surface-container-lowest shadow-sm flex items-center justify-center text-on-surface group-hover:text-primary transition-colors">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15.8333 0H4.16667C1.86583 0 0 1.86583 0 4.16667V15.8333C0 18.1342 1.86583 20 4.16667 20H15.8333C18.135 20 20 18.1342 20 15.8333V4.16667C20 1.86583 18.135 0 15.8333 0ZM6.66667 15.8333H4.16667V6.66667H6.66667V15.8333ZM5.41667 5.61C4.61167 5.61 3.95833 4.95167 3.95833 4.14C3.95833 3.32833 4.61167 2.67 5.41667 2.67C6.22167 2.67 6.875 3.32833 6.875 4.14C6.875 4.95167 6.2225 5.61 5.41667 5.61ZM16.6667 15.8333H14.1667V11.1633C14.1667 8.35667 10.8333 8.56917 10.8333 11.1633V15.8333H8.33333V6.66667H10.8333V8.1375C11.9967 5.9825 16.6667 5.82333 16.6667 10.2008V15.8333Z" fill="currentColor"/>
                    </svg>
                </div>
                <span class="text-body-sm font-bold text-primary tracking-tight">LinkedIn</span>
            </a>
        @else
            <div class="flex flex-col items-center justify-center p-md bg-surface-container/30 border border-outline-variant/10 rounded-2xl gap-sm text-center shadow-2xs opacity-40 cursor-not-allowed" 
                    title="Tidak melampirkan akun LinkedIn">
                <div class="w-10 h-10 rounded-full bg-surface-container-low flex items-center justify-center text-secondary/40">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15.8333 0H4.16667C1.86583 0 0 1.86583 0 4.16667V15.8333C0 18.1342 1.86583 20 4.16667 20H15.8333C18.135 20 20 18.1342 20 15.8333V4.16667C20 1.86583 18.135 0 15.8333 0ZM6.66667 15.8333H4.16667V6.66667H6.66667V15.8333ZM5.41667 5.61C4.61167 5.61 3.95833 4.95167 3.95833 4.14C3.95833 3.32833 4.61167 2.67 5.41667 2.67C6.22167 2.67 6.875 3.32833 6.875 4.14C6.875 4.95167 6.2225 5.61 5.41667 5.61ZM16.6667 15.8333H14.1667V11.1633C14.1667 8.35667 10.8333 8.56917 10.8333 11.1633V15.8333H8.33333V6.66667H10.8333V8.1375C11.9967 5.9825 16.6667 5.82333 16.6667 10.2008V15.8333Z" fill="currentColor"/>
                    </svg>
                </div>
                <span class="text-body-sm font-bold text-secondary/40 tracking-tight">LinkedIn</span>
            </div>
        @endif

    </div>
</div>
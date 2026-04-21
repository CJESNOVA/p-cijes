<?php

namespace App\Http\View\Composers;

use App\Main\SidebarPanel;
use Illuminate\View\View;

class SidebarComposer
{
    /**
     * Bind data to the view.
     *
     * @param View $view
     * @return void
     */
    public function compose(View $view)
    {
        if (!is_null(request()->route())) {
            //$pageName = request()->route()->getName();
            $pageName = request()->path();
            $routePrefix = explode('/', $pageName)[0] ?? '';

            switch ($routePrefix) {
                case 'membres':
                    $view->with('sidebarMenu', SidebarPanel::membres());
                    break;
                case 'entreprises':
                    $view->with('sidebarMenu', SidebarPanel::entreprises());
                    break;
                case 'diagnostics':
                    $view->with('sidebarMenu', SidebarPanel::diagnostics());
                    break;
                case 'accompagnements':
                    $view->with('sidebarMenu', SidebarPanel::accompagnements());
                    break;
                case 'evenements':
                    $view->with('sidebarMenu', SidebarPanel::evenements());
                    break;
                case 'experts':
                    $view->with('sidebarMenu', SidebarPanel::experts());
                    break;
                case 'prestations':
                    $view->with('sidebarMenu', SidebarPanel::prestations());
                    break;
                case 'bons':
                    $view->with('sidebarMenu', SidebarPanel::bons());
                    break;
                case 'forums':
                    $view->with('sidebarMenu', SidebarPanel::forums());
                    break;
                case 'elements':
                    $view->with('sidebarMenu', SidebarPanel::elements());
                    break;
                case 'components':
                    $view->with('sidebarMenu', SidebarPanel::components());
                    break;
                case 'forms':
                    $view->with('sidebarMenu', SidebarPanel::forms());
                    break;
                case 'layouts':
                    $view->with('sidebarMenu', SidebarPanel::layouts());
                    break;
                case 'apps':
                    $view->with('sidebarMenu', SidebarPanel::apps());
                    break;
                case 'dashboards':
                    $view->with('sidebarMenu', SidebarPanel::dashboards());
                    break;
                default:
                    $view->with('sidebarMenu', SidebarPanel::dashboards());
            }
            
            $view->with('allSidebarItems', SidebarPanel::all());
            $view->with('pageName', $pageName);
            $view->with('routePrefix', $routePrefix);
        }
    }
}

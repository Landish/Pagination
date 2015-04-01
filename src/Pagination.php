<?php namespace Landish\Pagination;

use Illuminate\Contracts\Pagination\Paginator as PaginatorContract;
use Illuminate\Contracts\Pagination\Presenter as PresenterContract;
use Illuminate\Pagination\UrlWindow;
use Illuminate\Pagination\UrlWindowPresenterTrait;

class Pagination implements PresenterContract {

    use UrlWindowPresenterTrait;

    /**
     * The paginator implementation.
     *
     * @var \Illuminate\Contracts\Pagination\Paginator
     */
    protected $paginator;

    /**
     * The URL window data structure.
     *
     * @var array
     */
    protected $window;

    /**
     * Pagination wrapper HTML.
     *
     * @var string
     */
    protected $paginationWrapper = '<ul class="pagination">%s %s %s</ul>';

    /**
     * Available page wrapper HTML.
     *
     * @var string
     */
    protected $availablePageWrapper = '<li><a href="%s">%s</a></li>';

    /**
     * Get active page wrapper HTML.
     *
     * @var string
     */
    protected $activePageWrapper = '<li class="active"><span>%s</span></li>';

    /**
     * Get disabled page wrapper HTML.
     *
     * @var string
     */
    protected $disabledPageWrapper = '<li class="disabled"><span>%s</span></li>';

    /**
     * Previous button text.
     *
     * @var string
     */
    protected $previousButtonText = '&laquo;';

    /**
     * Next button text.
     *
     * @var string
     */
    protected $nextButtonText = '&raquo;';

    /**
     * Create a new Pagination presenter instance.
     *
     * @param  \Illuminate\Contracts\Pagination\Paginator $paginator
     * @param  \Illuminate\Pagination\UrlWindow|null $window
     */
    public function __construct(PaginatorContract $paginator, UrlWindow $window = null)
    {
        $this->paginator = $paginator;
        $this->window = is_null($window) ? UrlWindow::make($paginator) : $window->get();
    }

    /**
     * Determine if the underlying paginator being presented has pages to show.
     *
     * @return bool
     */
    public function hasPages()
    {
        return $this->paginator->hasPages();
    }

    /**
     * Get pagination wrapper HTML.
     *
     * @return string
     */
    protected function getPaginationWrapperHTML() {
        return $this->paginationWrapper;
    }

    /**
     * Get available page wrapper HTML.
     *
     * @return string
     */
    protected function getAvailablePageWrapperHTML() {
        return $this->availablePageWrapper;
    }

    /**
     * Get active page wrapper HTML.
     *
     * @return string
     */
    protected function getActivePageWrapperHTML() {
        return $this->activePageWrapper;
    }

    /**
     * Get disabled page wrapper HTML.
     *
     * @return string
     */
    protected function getDisabledPageWrapperHTML() {
        return $this->disabledPageWrapper;
    }

    /**
     * Get previous button text.
     *
     * @return string
     */
    protected function getPreviousButtonText() {
        return $this->previousButtonText;
    }

    /**
     * Get next button text.
     *
     * @return string
     */
    protected function getNextButtonText() {
        return $this->nextButtonText;
    }

    /**
     * Convert the URL window into Pagination HTML.
     *
     * @return string
     */
    public function render()
    {
        if ($this->hasPages())
        {
            return sprintf(
                $this->getPaginationWrapperHTML(),
                $this->getPreviousButton(),
                $this->getLinks(),
                $this->getNextButton()
            );
        }

        return '';
    }

    /**
     * Get HTML wrapper for an available page link.
     *
     * @param  string $url
     * @param  int $page
     * @return string
     */
    protected function getAvailablePageWrapper($url, $page)
    {
        return sprintf($this->getAvailablePageWrapperHTML(),$url, $page);
    }

    /**
     * Get HTML wrapper for disabled text.
     *
     * @param  string  $text
     * @return string
     */
    protected function getDisabledTextWrapper($text)
    {
        return sprintf($this->getDisabledPageWrapperHTML(), $text);
    }

    /**
     * Get HTML wrapper for active text.
     *
     * @param  string  $text
     * @return string
     */
    protected function getActivePageWrapper($text)
    {
        return sprintf($this->getActivePageWrapperHTML(), $text);
    }

    /**
     * Get a pagination "dot" element.
     *
     * @return string
     */
    protected function getDots()
    {
        return $this->getDisabledTextWrapper("...");
    }

    /**
     * Get the current page from the paginator.
     *
     * @return int
     */
    protected function currentPage()
    {
        return $this->paginator->currentPage();
    }

    /**
     * Get the last page from the paginator.
     *
     * @return int
     */
    protected function lastPage()
    {
        return $this->paginator->lastPage();
    }

    /**
     * Get HTML wrapper for a page link.
     *
     * @param  string $url
     * @param  int $page
     * @return string
     */
    protected function getPageLinkWrapper($url, $page)
    {
        if ($page == $this->paginator->currentPage())
        {
            return $this->getActivePageWrapper($page);
        }

        return $this->getAvailablePageWrapper($url, $page);
    }

    /**
     * Get the previous page pagination element.
     * @return string
     * @internal param string $text
     */
    protected function getPreviousButton()
    {
        // If the current page is less than or equal to one, it means we can't go any
        // further back in the pages, so we will render a disabled previous button
        // when that is the case. Otherwise, we will give it an active "status".
        if ($this->paginator->currentPage() <= 1) {
            return $this->getDisabledTextWrapper($this->getPreviousButtonText());
        }

        $url = $this->paginator->url(
            $this->paginator->currentPage() - 1
        );

        return $this->getPageLinkWrapper($url, $this->getPreviousButtonText());
    }

    /**
     * Get the next page pagination element.
     * @return string
     */
    protected function getNextButton()
    {
        // If the current page is greater than or equal to the last page, it means we
        // can't go any further into the pages, as we're already on this last page
        // that is available, so we will make it the "next" link style disabled.
        if (!$this->paginator->hasMorePages()) {
            return $this->getDisabledTextWrapper($this->getNextButtonText());
        }

        $url = $this->paginator->url($this->paginator->currentPage() + 1);

        return $this->getPageLinkWrapper($url, $this->getNextButtonText());
    }

}

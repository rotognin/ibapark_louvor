<?php

namespace Funcoes\Layout;

class Form
{
    protected string $title;
    protected string $form = "";
    protected string $onSubmit = "";
    protected string $actions = "";
    protected bool $collapsable = false;
    protected bool $collapsed = false;
    protected array $hidden = [];
    protected array $fields = [];
    protected bool $cardFormat = true;
    protected string $titleTag = "h3";
    protected string $classCardFooter = "";
    protected string $internalPadding = "";
    protected string $ownerUser = "";
    protected string $userAction = "";

    /**
     * Setar o usuário "dono" do registro. Se outro usuário acessar o registro, não poderá alterá-lo
     */
    public function setOwnerUser(string $user)
    {
        $this->ownerUser = $user;
    }

    /**
     * Setar uma ação para que o form possa ser alterado caso o usuário logado tenha a ação.
     */
    public function setUserAction(string $acao)
    {
        $this->userAction = $acao;
    }

    public function setInternalPadding(string $padding)
    {
        $this->internalPadding = $padding;
    }

    public function setClassCardFooter(string $classCardFooter)
    {
        $this->classCardFooter = $classCardFooter;
    }

    public function setTitleTag(string $tag)
    {
        $this->titleTag = $tag;
    }

    public function addHidden(string $element)
    {
        $this->hidden[] = $element;
    }

    public function setCardFormat(bool $cardFormat = true)
    {
        $this->cardFormat = $cardFormat;
    }

    public function setFields(array $fields)
    {
        $this->fields = $fields;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function setActions(string $actions)
    {
        $this->actions = $actions;
    }

    public function setCollapsable(bool $collapsable)
    {
        $this->collapsable = $collapsable;
    }

    public function setCollapsed(bool $collapsed)
    {
        $this->collapsed = $collapsed;
    }

    public function setForm($attrs, $onSubmit = "")
    {
        $this->form = $attrs;
        $this->onSubmit = $onSubmit;
    }

    public function newRow(string $fields)
    {
        return '<div class="row">' . $fields . '</div>';
    }

    public function html()
    {
        global $session;
        global $activeUser;

        $usuario = $session->get('credentials.default');

        $formID = uniqid('form');

        $hidden = "";
        foreach ($this->hidden as $element) {
            $hidden .= $element;
        }

        $fields = "";
        foreach ($this->fields as $row) {
            if (empty($row)) {
                continue;
            }
            $fields .= "<div class=\"row\">";
            foreach ($row as $col) {
                $fields .= "<div class=\"col\">$col</div>";
            }
            $fields .= "</div>";
        }

        $collapseHTML = "";
        if ($this->collapsable) {
            $collapseHTML = <<<HTML
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
            HTML;

            if ($this->collapsed) {
                $collapseHTML .= <<<Javascript
                <script>
                    $(function() {
                        $('#$formID').CardWidget('collapse');
                    });
                </script>
                Javascript;
            }
        }

        $collapsed = $this->collapsed ? 'style="display:none"' : '';

        $cardFooter = ($this->cardFormat) ? 'card-footer' : '';

        $setActions = true;

        // Checar se o usuário é "dono" do registro
        $dono = true;

        if (!empty($this->ownerUser)) {
            if ($usuario != $this->ownerUser) {
                $setActions = false;
                $dono = false;
            }
        }

        // Se o usuário tiver a ação informada, irá sobrescrever a permissão de "dono" setada acima
        if (!empty($this->userAction)) {
            $permiteAcao = $activeUser->checkAction($this->userAction);

            if (!$permiteAcao && !$dono) {
                $setActions = false;
            }

            // A ação sobrescreve se não for dono
            if ($permiteAcao && !$dono) {
                $setActions = true;
            }
        }

        $actions = "";

        if ($setActions) {
            if (!empty($this->actions)) {
                $actions = <<<HTML
                    <div class="$cardFooter {$this->classCardFooter}" $collapsed>
                        $this->actions
                    </div>
                HTML;
            }
        }

        $cardClass = ($this->cardFormat) ? 'card card-primary card-outline' : '';
        $cardHeader = '';
        $cardBody = '';

        if ($this->cardFormat) {
            $cardTitle = ($this->titleTag != 'h3') ? 'p-0' : 'card-title';
            $classAdd = ($this->titleTag != 'h3') ? 'pl-2 pt-2 pr-0 pb-0' : '';

            $cardHeader = <<<HEADER
                <div class="card-header {$classAdd}">
                    <{$this->titleTag} class="{$cardTitle} font-weight-bold">$this->title</{$this->titleTag}>
                    $collapseHTML
                </div>
            HEADER;

            $cardBody = 'card-body';
        }

        $marginBetween = ($this->titleTag != 'h3') ? 'mb-0' : '';

        $form = <<<HTML
        $hidden
        <div class="$cardClass {$marginBetween}" id="$formID">
            $cardHeader
            <div class="$cardBody {$this->internalPadding}" $collapsed>
                $fields
            </div>
            $actions
        </div>
        HTML;

        if (!empty($this->form)) {
            $form = <<<HTML
            <form $this->form onsubmit="$this->onSubmit" autocomplete="off" novalidate="novalidate">
                $form
            </form>
            HTML;
        }
        return $form;
    }
}

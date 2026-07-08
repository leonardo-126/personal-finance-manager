<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CaixasFinaceiras\CreateCaixaFinanceira;
use App\Http\Controllers\CaixasFinaceiras\DeleteCaixaFinanceira;
use App\Http\Controllers\CaixasFinaceiras\ShowCaixaFinanceira;
use App\Http\Controllers\CaixasFinaceiras\UpdateCaixaFinanceira;
use App\Http\Controllers\CategoriasGastos\CreateCategoriaGasto;
use App\Http\Controllers\CategoriasGastos\DeleteCategoriaGasto;
use App\Http\Controllers\CategoriasGastos\ShowCategoriaGasto;
use App\Http\Controllers\CategoriasGastos\UpdateCategoriaGasto;
use App\Http\Controllers\Faturas\ImportarFatura;
use App\Http\Controllers\Faturas\PreviewFatura;
use App\Http\Controllers\Faturas\ShowFaturas;
use App\Http\Controllers\Gastos\CreateGasto;
use App\Http\Controllers\Gastos\DeleteGasto;
use App\Http\Controllers\Gastos\ShowGasto;
use App\Http\Controllers\Gastos\ShowGastoDetalhes;
use App\Http\Controllers\Gastos\UpdateGasto;
use App\Http\Controllers\GastosItens\CreateGastoItem;
use App\Http\Controllers\GastosItens\DeleteGastoItem;
use App\Http\Controllers\GastosItens\ShowGastoItem;
use App\Http\Controllers\GastosItens\UpdateGastoItem;
use App\Http\Controllers\GastosItens\AtribuirPessoaItem;
use App\Http\Controllers\Pessoas\CreatePessoa;
use App\Http\Controllers\Pessoas\DeletePessoa;
use App\Http\Controllers\Pessoas\ShowPessoa;
use App\Http\Controllers\Pessoas\UpdatePessoa;
use App\Http\Controllers\MovimentacoesCaixas\CreateMovimentacaoCaixa;
use App\Http\Controllers\MovimentacoesCaixas\DeleteMovimentacaoCaixa;
use App\Http\Controllers\MovimentacoesCaixas\ShowMovimentacaoCaixa;
use App\Http\Controllers\MovimentacoesCaixas\UpdateMovimentacaoCaixa;
use App\Http\Controllers\fontes_renda\createFontesRenda;
use App\Http\Controllers\fontes_renda\DeleteFontesRenda;
use App\Http\Controllers\fontes_renda\ShowFontesRenda;
use App\Http\Controllers\fontes_renda\UpdateFontesRenda;
use App\Http\Controllers\Profile\CreateProfile;
use App\Http\Controllers\Profile\ShowProfile;
use App\Http\Controllers\Profile\UpdateProfile;
use App\Http\Controllers\Rendas\CreateRenda;
use App\Http\Controllers\Rendas\DeleteRenda;
use App\Http\Controllers\Rendas\ShowRenda;
use App\Http\Controllers\Rendas\UpdateRenda;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('/auth/register', RegisterController::class);
Route::post('/auth/login',    LoginController::class);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', LogoutController::class);
    Route::get('/auth/me', fn(Request $r) => new UserResource($r->user()->load('profile')));

    Route::get('/profile',  ShowProfile::class);
    Route::post('/profile', CreateProfile::class);
    Route::put('/profile',  UpdateProfile::class);

    // Fontes de renda
    Route::get('/fontes-renda',          ShowFontesRenda::class);
    Route::post('/fontes-renda',         createFontesRenda::class);
    Route::put('/fontes-renda/{id}',     UpdateFontesRenda::class);
    Route::delete('/fontes-renda/{id}',  DeleteFontesRenda::class);

    // Rendas
    Route::get('/rendas',          ShowRenda::class);
    Route::post('/rendas',         CreateRenda::class);
    Route::put('/rendas/{id}',     UpdateRenda::class);
    Route::delete('/rendas/{id}',  DeleteRenda::class);

    // Caixas financeiras
    Route::get('/caixas-financeiras',          ShowCaixaFinanceira::class);
    Route::post('/caixas-financeiras',         CreateCaixaFinanceira::class);
    Route::put('/caixas-financeiras/{id}',     UpdateCaixaFinanceira::class);
    Route::delete('/caixas-financeiras/{id}',  DeleteCaixaFinanceira::class);

    // Movimentações de caixas
    Route::get('/movimentacoes-caixas',          ShowMovimentacaoCaixa::class);
    Route::post('/movimentacoes-caixas',         CreateMovimentacaoCaixa::class);
    Route::put('/movimentacoes-caixas/{id}',     UpdateMovimentacaoCaixa::class);
    Route::delete('/movimentacoes-caixas/{id}',  DeleteMovimentacaoCaixa::class);

    // Categorias de gastos
    Route::get('/categorias-gastos',          ShowCategoriaGasto::class);
    Route::post('/categorias-gastos',         CreateCategoriaGasto::class);
    Route::put('/categorias-gastos/{id}',     UpdateCategoriaGasto::class);
    Route::delete('/categorias-gastos/{id}',  DeleteCategoriaGasto::class);

    // Gastos
    Route::get('/gastos',          ShowGasto::class);
    Route::get('/gastos/{id}',     ShowGastoDetalhes::class);
    Route::post('/gastos',         CreateGasto::class);
    Route::put('/gastos/{id}',     UpdateGasto::class);
    Route::delete('/gastos/{id}',  DeleteGasto::class);

    // Itens de gastos
    Route::get('/gastos-itens',          ShowGastoItem::class);
    Route::post('/gastos-itens',         CreateGastoItem::class);
    Route::put('/gastos-itens/{id}',     UpdateGastoItem::class);
    Route::patch('/gastos-itens/{id}/pessoa', AtribuirPessoaItem::class);
    Route::delete('/gastos-itens/{id}',  DeleteGastoItem::class);

    // Pessoas (quem usou o cartão)
    Route::get('/pessoas',          ShowPessoa::class);
    Route::post('/pessoas',         CreatePessoa::class);
    Route::put('/pessoas/{id}',     UpdatePessoa::class);
    Route::delete('/pessoas/{id}',  DeletePessoa::class);

    // Faturas de cartão (importação Nubank)
    Route::get('/faturas',            ShowFaturas::class);
    Route::post('/faturas/preview',   PreviewFatura::class);
    Route::post('/faturas/importar',  ImportarFatura::class);
});

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->string('type', 20)->index();
            $table->timestamps();

            // Data Registazione: Campo Data
            $table->date('reg_date')->nullable();
            // Data Documento: Campo Data
            $table->date('doc_date')->nullable();
            // Protocollo Interno: numero progressivo automatico
            $table->string('int_pr');
            // Protocollo Esterno: campo di testo
            $table->string('ext_pr');
            // Cod. Mittente:Select
            $table->string('sender_code'); // ?
            // Mittente: campo di testo
            $table->string('sender_name'); // ?
            // Descrizione: testo lungo
            $table->text('notes');
            // Ufficio Responsabile: Select
            $table->string('office'); // ?
            // Mezzo di arrivo: Select
            $table->string('mean_of_arrival'); // ?
            // Condizionali, in base alla selezione di un campo gli altri campi variano di contenuto
            // Titoli; Classe; Sottoclasse; Fascicolo.
            $table->string('dossier'); // ?
            // Locazione: Select
            $table->string('location'); // ?
            // Upload: upload scannerizzazione documento (da rendere visibile nel record in visualizzazione lettura)
            $table->string('file_token')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}

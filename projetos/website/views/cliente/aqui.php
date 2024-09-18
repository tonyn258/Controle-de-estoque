<div class="box-body">
    <div class="form-group">

        <div class="col-sm-6">
            <label for="exampleInputEmail1">CPF/CNPJ</label>
            <input type="text" name="cpfCliente" class="form-control" id="exampleInputEmail1" placeholder="CPF/CNPJ">
        </div>

        <div class="col-sm-7">
            <label for="exampleInputEmail1">Nome Cliente</label>
            <input type="text" name="NomeCliente" class="form-control" id="exampleInputEmail1" placeholder="Nome Cliente">
        </div>

    </div>

    <div class="form-group row">

        <div class="col-sm-3">
            <label for="exampleInputEmail1">Cep</label>
            <input type="text" name="FoneCliente" class="form-control" id="exampleInputEmail1" placeholder="Cep">
        </div>

        <div class="col-sm-3">
            <label for="exampleInputEmail1">Cidade</label>
            <input type="text" name="Cidade" class="form-control" id="exampleInputEmail1" placeholder="Cidade Cliente">
        </div>

        <div class="col-sm-3">
            <label for="exampleInputEmail1">UF</label>
            <select name="UF" class="form-control" required>
                <option value="">Selecione um estado</option>
                <option value="AC">Acre</option>
                <option value="AL">Alagoas</option>
                <option value="AP">Amapá</option>
                <option value="AM">Amazonas</option>
                <option value="BA">Bahia</option>
                <option value="CE">Ceará</option>
                <option value="DF">Distrito Federal</option>
                <option value="ES">Espírito Santo</option>
                <option value="GO">Goiás</option>
                <option value="MA">Maranhão</option>
                <option value="MT">Mato Grosso</option>
                <option value="MS">Mato Grosso do Sul</option>
                <option value="MG">Minas Gerais</option>
                <option value="PA">Pará</option>
                <option value="PB">Paraíba</option>
                <option value="PR">Paraná</option>
                <option value="PE">Pernambuco</option>
                <option value="PI">Piauí</option>
                <option value="RJ">Rio de Janeiro</option>
                <option value="RN">Rio Grande do Norte</option>
                <option value="RS">Rio Grande do Sul</option>
                <option value="RO">Rondônia</option>
                <option value="RR">Roraima</option>
                <option value="SC">Santa Catarina</option>
                <option value="SP">São Paulo</option>
                <option value="SE">Sergipe</option>
                <option value="TO">Tocantins</option>
            </select>
        </div>
    </div>



    <input type="hidden" name="iduser" value="' . $idUsuario . '">
</div>
<div class="box-footer">
    <button type="submit" name="upload" class="btn btn-primary" value="Cadastrar">Cadastrar</button>
    <a class="btn btn-danger" href="../../views/cliente">Cancelar</a>
</div>
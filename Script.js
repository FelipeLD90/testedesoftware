function cAJAX() {
	this.Url = '';
	this.FunctionName = 'fAJAX()';
	this.Execute = function () {
		var functionName = this.FunctionName;
		var msg = '';
		url = this.Url;
		var oXMLhttp = GetXmlHttpObject();
		if (oXMLhttp == null) {
			msg = 'Recurso AJAX não suportado ou está bloqueado, revise as configrações, atualize o navegador e ODBC.';
			if (oMsg) { oMsg.innerText = msg } else { window.status = msg };
		} else {
			msg = 'Consulta AJAX sendo realizada, aguarde...';
			if (oMsg) { oMsg.innerText = msg } else { window.status = msg };
			oXMLhttp.onreadystatechange = function () {
				if (oXMLhttp.readyState == 4 || oXMLhttp.readyState == 'complete') {
					msg = 'Consulta AJAX finalizada';
					if (oMsg) { oMsg.innerText = msg } else { window.status = msg };
					if (oXMLhttp.status == 200) {
						functionName = functionName.replace('()', '(oXMLhttp.responseText)');
						msg = 'Consulta AJAX finalizada.';
						if (oMsg) { oMsg.innerText = msg } else { window.status = msg };
						eval(functionName); // executa funcao
					} else {
						msg = 'Consulta AJAX sendo realizada, aguarde...';
						if (oMsg) { oMsg.innerText = msg } else { window.status = msg };
					};
					if(oXMLhttp.status == 404) {
						msg = 'Consulta AJAX não retornou dados, possivel página não encontrada.';
						if (oMsg) { oMsg.innerText = msg } else { window.status = msg };
						return;
					};
				};
			};
			oXMLhttp.open('GET', url, true);
			oXMLhttp.send();
		};
	};
};

function GetXmlHttpObject()
{
	try { return new ActiveXObject('Msxml2.XMLHTTP') }
	catch(e) {
		try { return new ActiveXObject('Microsoft.XMLHTTP') }
		catch(e) {
			try { return new XMLHttpRequest() }
			catch(e) { return null }
		};
	};
};

function fGo(link) {
	var aa = document.getElementById('aMenu');
	aa.href = link;
	aa.click();
}

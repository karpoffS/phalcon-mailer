<html>
<head></head>
<style>
	.reg {
		box-sizing: border-box;
		background-color: rgb(240, 173, 78);
		color: rgb(255, 255, 255)/* white */;
		text-decoration: none;
		display: inline-block;
		margin-bottom: 0px;
		font-weight: normal;
		text-align: center;
		white-space: nowrap;
		vertical-align: middle;
		touch-action: manipulation;
		cursor: pointer;
		-webkit-user-select: none;
		background-image: linear-gradient(rgb(240, 173, 78) 0%, rgb(235, 147, 22) 100%);
		text-shadow: rgba(0, 0, 0, 0.2) 0px -1px 0px;
		box-shadow: rgba(255, 255, 255, 0.14902) 0px 1px 0px inset, rgba(0, 0, 0, 0.0745098) 0px 1px 1px;
		background-repeat-x: repeat;
		background-repeat-y: no-repeat;
		border: 1px solid rgb(227, 141, 19)/* goldenrod */;
		border-radius: 2px;
		-moz-border-radius: 2px;
		-webkit-border-radius: 2px;
		padding: 0x 3px;
	}
	.reg:hover {
		color: rgb(255, 255, 255)/* white */;
		text-decoration: none;
		background-color: rgb(235, 147, 22);
		background-position-x: 0px;
		background-position-y: -15px;
		border-color: rgb(213, 133, 18)/* darkgoldenrod */;
	}
	.reg:focus {
		color: rgb(255, 255, 255)/* white */;
		text-decoration: none;
		outline-offset: -2px;
		background-color: rgb(235, 147, 22);
		background-position-x: 0px;
		background-position-y: -15px;
		border-color: rgb(152, 95, 13)/* saddlebrown */;
		outline: -webkit-focus-ring-color 5px auto;
	}
	.reg:active {
		background-image: none;
		box-shadow: rgba(0, 0, 0, 0.121569) 0px 3px 5px inset;
		color: rgb(255, 255, 255)/* white */;
		background-color: rgb(235, 147, 22);
		border-color: rgb(227, 141, 19)/* goldenrod */;
	}

</style>
<body style="background-color: #E4E4E4;padding: 20px; margin: 0; min-width: 640px;">
<table border="0" cellspacing="0" width="530" style="color:#262626;background-color:#fff;
			padding:27px 30px 20px 30px;margin:auto; border:1px solid #e1e1e1;">
	<tbody>
	<!-- header -->
	<tr>
		<td style="padding-left:20px;box-sizing: border-box;background-color: rgb(34, 34, 34);background-image: linear-gradient(rgb(60, 60, 60) 0%, rgb(34, 34, 34) 100%);background-repeat-x: repeat;background-repeat-y: no-repeat;border: 1px solid rgb(8, 8, 8);border-radius: 4px;-moz-border-radius: 4px;-webkit-border-radius: 4px;">
			<a target="_blank" style="text-decoration:none;color:inherit;font-family:'HelveticaNeue','Helvetica Neue',Helvetica,Arial,sans-serif;font-weight:normal;">
				<h1 style="color:#fff">
					<img src="data:image/png;base64,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" style="position: relative;top: 12px;left: 7px;">
					Mailer
				</h1>
			</a>
		</td>
	</tr>
	<tr>
		<td style="padding:40px 0  0 0;">
			<p style="color:#000;font-size: 16px;line-height:24px;font-family:'HelveticaNeue','Helvetica Neue',Helvetica,Arial,sans-serif;font-weight:normal;">

			<h2 style="font-size: 14px;font-family:'HelveticaNeue','Helvetica Neue',Helvetica,Arial,sans-serif;">Поздравляем!</h2>

			<p style="font-size: 13px;line-height:24px;font-family:'HelveticaNeue','Helvetica Neue',Helvetica,Arial,sans-serif;">
				Вас пригласили на <strong style="color: firebrick;">Альфа</strong> тестирование в сервисе Mailer!
				<br>
				Сервис предоставляет возможность делать рассылку писем по следующим параметрам:
					<ul>
						<li>Список рассылки который вы загружаете из csv формата</li>
						<li>Группа шаблонов писем для быстрой навигации</li>
						<li>Создание шаблонов писем для нужной вам рассылки</li>
					</ul>
				<br>
				В дальнейшем сервис будет только расти и развиваться все ваши предложения по улучшению ждём по адресу: <a href="mailto:karpoff@simplestartup.pro">karpoff@simplestartup.pro</a>.
			</p>
			<p style="font-size: 13px;line-height:24px;font-family:'HelveticaNeue','Helvetica Neue',Helvetica,Arial,sans-serif;">
				Вы можете перейти по ссылке <a class="reg" href="http://{{ publicUrl }}/signup">Регистрации</a> чтобы создать учётную запись в сервисе Mailer.
				<br>
					Для успешной регистрации воспользуйтесь следующим кодом приглашения:
					<b>{{ inviteCode }}</b>
				<br>
				{#<br>#}
					{#Или воспользуйтесь ссылкой для перехода регистрации расположеной ниже.#}
				{#<br>#}
				{#<a style="color:#E86537;" href="http://{{ publicUrl }}{{ inviteUrl }}">Ссылка регистрации!</a>#}
				<br>
				<br>
				Mailer!. Приятной вам работы!
				<br>
			</p>
		</td>
	</tr>
	</tbody>

	<!--footer-->
</table>
</body>
</html>



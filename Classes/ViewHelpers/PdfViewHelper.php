<?php

class Tx_Dompdf_ViewHelpers_PdfViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper{

	/**
	 * @var int
	 */
	protected $errorReporting = 0;

	/**
	 * @todo add support for saving and linking the pdf
	 */
	function initializeArguments() {
		$this->registerArgument('debug',        'boolean','debug or not',           0, 0);
		$this->registerArgument('filename',     'string', 'filename for download',  0, 'output.pdf');
		$this->registerArgument('papersize',    'string', 'set the papersize',      0, 'A4');
		$this->registerArgument('orientation',  'string', 'set the orientation',    0, 'portrait');
		$this->registerArgument('basepath',     'string', 'set the basepath',       0, '');
		$this->registerArgument('redirect',     'boolean', 'enable redirect',       0, FALSE);
	}

	/**
	 *
	 * @return string the rendered string
	 * @api
	 */
	public function render() {
		ini_set('memory_limit', '512M');

		ob_end_clean();

		$buffer   = $this->renderChildren();
		$filename = '';

		if($this->arguments['redirect']) {
			$basename = str_replace('.pdf', '-' . sha1($buffer) . '.pdf', $this->arguments['filename']);
			$fileHelper = new t3lib_basicFileFunctions();
			$basename = $fileHelper->cleanFileName($basename);
			$filename = PATH_site . 'typo3temp/dompdf/' . $basename;
			t3lib_div::mkdir_deep(PATH_site . 'typo3temp/', 'dompdf');
			$this->redirectIfExists($filename);
		}

		$this->renderPdfFromHtmlWithDomPdf(
			$buffer,
			0,
			$filename
		);

		$this->redirectIfExists($filename);

		exit;
	}

	protected function redirectIfExists($filename) {
		if(($filename !== '') && (file_exists($filename))) {
			$filename = str_replace(PATH_site, '', $filename);
			header('Location: /' . $filename);
			exit();
		}
	}

	protected  function disableErrorReporting() {
		$this->errorReporting = error_reporting();
		#error_reporting(E_ERROR | E_PARSE);
	}

	protected function enableErrorReporting() {
		error_reporting($this->errorReporting);
	}

	protected function renderPdfFromHtmlWithDomPdf($html, $forceDownload = 0, $filename = '') {
		$this->disableErrorReporting();

		require_once(t3lib_extMgm::extPath('dompdf') . 'Resources/Private/Contrib/dompdf/dompdf_config.inc.php');
		$domPdf = new DOMPDF();
		$domPdf->set_paper(
			$this->arguments['papersize'],
			$this->arguments['orientation']
		);
		$domPdf->set_base_path($this->arguments['basepath']);
		$domPdf->load_html($html);
		$domPdf->render();

		if($this->arguments['redirect']) {
			file_put_contents($filename, $domPdf->output());
		} else {
			$domPdf->stream(
				$this->arguments['filename'],
				array(
					'Attachment' => $forceDownload
				)
			);
		}
		$this->enableErrorReporting();

		unset($domPdf);
	}
}
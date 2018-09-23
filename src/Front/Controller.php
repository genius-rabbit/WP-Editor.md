<?php

namespace EditormdFront;

use EditormdApp\WPComMarkdown;

class Controller {
	/**
	 * @var string 插件名称
	 */
	private $plugin_name;

	/**
	 * @var string 插件版本号
	 */
	private $version;

	/**
	 * @var string 翻译文本域
	 */
	private $text_domain;

	/**
	 * @var string 静态资源地址
	 */
	private $front_static_url;

	/**
	 * Controller constructor 初始化类并设置其属性
	 *
	 * @param $plugin_name
	 * @param $version
	 * @param $ioption
	 */
	public function __construct() {
		$this->plugin_name      = 'WP Editor.md';
		$this->text_domain      = 'editormd';
		$this->version          = WP_EDITORMD_VER;
		$this->front_static_url = $this->get_option( 'editor_addres', 'editor_style' );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front_scripts' ) );
	}

	/**
	 * 注册样式文件
	 */
	public function enqueue_front_styles() {
		//Style - Editor.md
		wp_enqueue_style( 'Editormd_Front', $this->front_static_url . '/assets/Editormd/editormd.min.css', array(), '2.0.1', 'all' );
		//Style - Config
		wp_enqueue_style( 'Config_Front', $this->front_static_url . '/assets/Config/editormd.min.css', array(), $this->version, 'all' );
	}

	/**
	 * 注册脚本文件
	 */
	public function enqueue_front_scripts() {

		//兼容模式 - jQuery
		if ( $this->get_option( 'jquery_compatible', 'editor_advanced' ) !== 'off' ) {
			wp_enqueue_script( 'jquery', null, null, array(), false );
		} else {
			wp_deregister_script( 'jquery' );
			//JavaScript - jQuery
			wp_enqueue_script( 'jQuery-CDN', $this->front_static_url . '/assets/jQuery/jquery.min.js', array(), '1.12.4', true );
		}

		//JavaScript - Turndown
		wp_enqueue_script( 'Turndown', $this->front_static_url . '/assets/Turndown/turndown.js', array(), '5.0.1', true );

		//JavaScript - Editormd
		wp_enqueue_script( 'Editormd_Front', $this->front_static_url . '/assets/Editormd/editormd.min.js', array( 'jQuery-CDN' ), '2.0.1', true );
		//JavaScript - Config
		wp_enqueue_script( 'Config_Front', $this->front_static_url . '/assets/Config/editormd.min.js', array( 'Editormd_Front' ), $this->version, true );

		//JavaScript - 载入国际化语言资源文件
		$lang = get_bloginfo( 'language' );
		switch ( $lang ) {
			case 'zh-TW':
				wp_enqueue_script( 'Editormd-lang-tw_Front', $this->front_static_url . '/assets/Editormd/languages/zh-tw.js', array(), '2.0.1', true );//载入台湾语言资源库
				break;
			case 'zh-CN':
				break;
			case 'en-US':
				wp_enqueue_script( 'Editormd-lang-us_Front', $this->front_static_url . '/assets/Editormd/languages/en.js', array(), '2.0.1', true );//载入美国英语语言资源库
				break;
			default:
				wp_enqueue_script( 'Editormd-lang-us_Front', $this->front_static_url . '/assets/Editormd/languages/en.js', array(), '2.0.1', true );//默认载入美国英语语言资源库
		}


		if ( $this->get_option( 'highlight_library_style', 'syntax_highlighting' ) == 'customize' ) {
			$prismTheme = 'default';
		} else {
			$prismTheme = $this->get_option( 'highlight_library_style', 'syntax_highlighting' );
		}

		wp_localize_script( 'Config_Front', 'Editormd', array(
			'editormdUrl'       => $this->front_static_url, //静态资源CDN地址
			'syncScrolling'     => $this->get_option( 'sync_scrolling', 'editor_basics' ), //编辑器同步
			'livePreview'       => $this->get_option( 'live_preview', 'editor_basics' ), //即是否开启实时预览
			'htmlDecode'        => $this->get_option( 'html_decode', 'editor_basics' ), //HTML标签解析
			'toc'               => $this->get_option( 'support_toc', 'editor_toc' ), //TOC
			'theme'             => $this->get_option( 'theme_style', 'editor_style' ), //编辑器总体主题
			'previewTheme'      => $this->get_option( 'theme_style', 'editor_style' ), //编辑器预览主题
			'editorTheme'       => $this->get_option( 'code_style', 'editor_style' ), //编辑器编辑主题
			'emoji'             => $this->get_option( 'support_emoji', 'editor_emoji' ), //emoji表情
			'taskList'          => $this->get_option( 'task_list', 'editor_basics' ), //task lists
			'imagePaste'        => $this->get_option( 'imagepaste', 'editor_basics' ), //图像粘贴
			'imagePasteSM'      => $this->get_option( 'imagepaste_sm', 'editor_basics' ), //图像粘贴上传源
			'prismTheme'        => $prismTheme, //语法高亮风格
			'prismLineNumbers'  => $this->get_option( 'line_numbers', 'syntax_highlighting' ), //行号显示
			'placeholderEditor' => __( 'Enjoy Markdown! Coding now...', $this->text_domain ),
			'imgUploading'      => __( 'Image Uploading...', $this->text_domain ),
			'imgUploadeFailed'  => __( 'Failed To Upload The Image!', $this->text_domain ),
			'supportComment'    => $this->get_option( 'support_front', 'editor_basics' ), // 前端评论
		) );
	}

	/**
	 * 获取字段值
	 *
	 * @param string $option  字段名称
	 * @param string $section 字段名称分组
	 * @param string $default 没搜索到返回空
	 *
	 * @return mixed
	 */
	public function get_option( $option, $section, $default = '' ) {

		$options = get_option( $section );

		if ( isset( $options[ $option ] ) ) {
			return $options[ $option ];
		}

		return $default;
	}

}

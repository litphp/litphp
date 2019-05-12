/**
 * Copyright (c) 2017-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

const React = require('react');

const CompLibrary = require('../../core/CompLibrary.js');

const MarkdownBlock = CompLibrary.MarkdownBlock;
/* Used to read markdown */
const Container = CompLibrary.Container;
const GridBlock = CompLibrary.GridBlock;

const siteConfig = require(`${process.cwd()}/siteConfig.js`);

function imgUrl(img) {
    return `${siteConfig.baseUrl}img/${img}`;
}

function docUrl(doc, language) {
    return `${siteConfig.baseUrl}docs/${language ? `${language}/` : ''}${doc}`;
}

function pageUrl(page, language) {
    return siteConfig.baseUrl + (language ? `${language}/` : '') + page;
}

class Button extends React.Component {
    render() {
        return (
            <div className="pluginWrapper buttonWrapper">
                <a className="button" href={this.props.href} target={this.props.target}>
                    {this.props.children}
                </a>
            </div>
        );
    }
}

Button.defaultProps = {
    target: '_self',
};

const SplashContainer = props => (
    <div className="homeContainer">
        <div className="homeSplashFade">
            <div className="wrapper homeWrapper">{props.children}</div>
        </div>
    </div>
);

const Logo = props => (
    <div className="projectLogo">
        <img src={props.img_src} alt="Project Logo"/>
    </div>
);

const ProjectTitle = () => (
    <h2 className="projectTitle">
        {siteConfig.title}
        <small>{siteConfig.tagline}</small>
    </h2>
);

const PromoSection = props => (
    <div className="section promoSection">
        <div className="promoRow">
            <div className="pluginRowBlock">{props.children}</div>
        </div>
    </div>
);

class HomeSplash extends React.Component {
    render() {
        const language = this.props.language || '';
        return (
            <SplashContainer>
                <Logo img_src={imgUrl('lit.svg')}/>
                <div className="inner">
                    <ProjectTitle/>
                    <PromoSection>
                        <Button href="#try">Try It Out</Button>
                        <Button href={docUrl('.', language)}>Documentation</Button>
                        <Button href="https://github.com/litphp/litphp">Source</Button>
                    </PromoSection>
                </div>
            </SplashContainer>
        );
    }
}

const Block = props => (
    <Container
        padding={['bottom', 'top']}
        id={props.id}
        background={props.background}>
        <GridBlock align={props.align || 'center'} contents={props.children} className={props.className}
                   layout={props.layout}/>
    </Container>
);

const Features = () => (
    <Block layout="twoColumn" background="dark">
        {[
            {
                content: `We love PSR standards.
                <br>
                Follow PSR whenever applicable (mainly container and HTTP family),
                <br>
                And we implement our own psr container
                `,
                image: imgUrl('phpfig.png'),
                imageAlign: 'top',
                title: '**PSR** compatible',
            },
            {
                content: `We try to be adpative facing both modern and legacy challenges and solutions.
                <br>
                Scales well from monolith to microservice; from complete application to api glue layer
                <br>
                Besides Restful API, GraphQL and GRPC are on roadmap
                `,
                image: imgUrl('heart.png'),
                imageAlign: 'top',
                title: '**Friends** of everyone',
            },
        ]}
    </Block>
);

const FeatureCallout = () => [
    <Block align="left" className="feature-callout">{[
        {
            content: `
**N**imo **i**s your **m**iddleware **o**rganizer
<br/>
In addition to middleware pipe, we have various basic middleware / handler bundled
        `,
            image: imgUrl('pipe.svg'),
            imageAlign: 'left',
            title: 'Middleware Organizer',
        },
    ]}</Block>,
    <Block align="left" className="feature-callout" background="light">{[
        {
            content: `
**Templating** is never whole world of **view**
<br>
**Router**, as a part of **controller**, should be optional
<br>
For your business **model**, we avoid making any assumption
        `,
            image: imgUrl('mvc.svg'),
            imageAlign: 'right',
            title: 'Rethinking MVC',
        },
    ]}</Block>,
    <Block align="left" className="feature-callout">{[
        {
            content: `
Simple & smart DI support via \`litphp/air\`
<br>
Or just [delegate](https://github.com/container-interop/container-interop/blob/HEAD/docs/Delegate-lookup.md) to any other PSR-11 container
        `,
            image: imgUrl('injector.svg'),
            imageAlign: 'left',
            title: 'Dependency Injection',
        },
    ]}</Block>,
];

const LearnHow = () => (
    <Block background="light">
        {[
            {
                content: 'Talk about learning how to use this',
                image: imgUrl('lit.svg'),
                imageAlign: 'bottom',
                title: 'Learn How',
            },
        ]}
    </Block>
);

const TryOut = () => (
    <Block id="try" background="light">
        {[
            {
                content: 'A quick taste of **bolt**, our micro framework',
                image: imgUrl('hello.png'),
                imageAlign: 'bottom',
                imageLink: docUrl('quickstart'),
                title: 'Try it out',
            },
        ]}
    </Block>
);

const Description = () => (
    <Block background="dark">
        {[
            {
                content: 'This is another description of how this project is useful',
                image: imgUrl('lit.svg'),
                imageAlign: 'right',
                title: 'Description',
            },
        ]}
    </Block>
);

const Showcase = props => {
    if ((siteConfig.users || []).length === 0) {
        return null;
    }

    const showcase = siteConfig.users.filter(user => user.pinned).map(user => (
        <a href={user.infoLink} key={user.infoLink}>
            <img src={user.image} alt={user.caption} title={user.caption}/>
        </a>
    ));

    return (
        <div className="productShowcaseSection paddingBottom">
            <h2>Who is Using This?</h2>
            <p>This project is used by all these people</p>
            <div className="logos">{showcase}</div>
            <div className="more-users">
                <a className="button" href={pageUrl('users.html', props.language)}>
                    More {siteConfig.title} Users
                </a>
            </div>
        </div>
    );
};

class Index extends React.Component {
    render() {
        const language = this.props.language || '';

        return (
            <div>
                <HomeSplash language={language}/>
                <div className="mainContainer">
                    <Features/>
                    <FeatureCallout/>
                    {/*<LearnHow/>*/}
                    <TryOut/>
                    {/*<Description/>*/}
                    {/*<Showcase language={language}/>*/}
                </div>
            </div>
        );
    }
}

module.exports = Index;

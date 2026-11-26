import { StrictMode, useEffect, useState } from 'react'
import { createRoot } from 'react-dom/client'
import { StillContext } from './kit/reduced-motion'
import { BenefileHero, type Lang } from './scene/BenefileHero'

// The page holds a still of the scene in #hero-demo, for visitors without
// JavaScript and while the script loads. The scene replaces it.
//
// The scene loops, so the page offers a button that stops it: paused, the
// scene shows its still. The button's words come from the page
// (data-pause, data-play), in the page's language. With reduced motion the
// scene is still already, and the button stays hidden.

function Hero({ toggle, lang }: { toggle: HTMLButtonElement | null; lang: Lang }) {
  const [paused, setPaused] = useState(false)

  useEffect(() => {
    if (!toggle) return
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)')
    const show = () => {
      toggle.hidden = reduced.matches
    }
    const click = () => setPaused((p) => !p)
    show()
    reduced.addEventListener('change', show)
    toggle.addEventListener('click', click)
    return () => {
      reduced.removeEventListener('change', show)
      toggle.removeEventListener('click', click)
    }
  }, [toggle])

  useEffect(() => {
    if (!toggle) return
    toggle.textContent = (paused ? toggle.dataset.play : toggle.dataset.pause) ?? ''
  }, [paused, toggle])

  return (
    <StillContext.Provider value={paused}>
      <BenefileHero lang={lang} />
    </StillContext.Provider>
  )
}

const host = document.getElementById('hero-demo')
if (host) {
  const toggle = document.getElementById('hero-demo-toggle') as HTMLButtonElement | null
  createRoot(host).render(
    <StrictMode>
      <Hero toggle={toggle} lang={document.documentElement.lang === 'el' ? 'el' : 'en'} />
    </StrictMode>,
  )
}
